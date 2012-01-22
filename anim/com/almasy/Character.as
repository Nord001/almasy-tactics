package com.almasy {
	import flash.display.*;
	import flash.text.*;
	import flash.net.*;
	import flash.events.*;
	import flash.display.*;
	import flash.filters.*;
	import org.papervision3d.cameras.Camera3D;
	import org.papervision3d.render.BasicRenderEngine;
	import org.papervision3d.scenes.Scene3D;
	import org.papervision3d.core.*;
	import org.papervision3d.core.proto.*;
	import org.papervision3d.materials.*;
	import org.papervision3d.materials.utils.*;
	import org.papervision3d.view.Viewport3D;
	import org.papervision3d.objects.primitives.*;
	import org.papervision3d.objects.*;
	import org.papervision3d.core.math.Number3D;
	
	public class Character extends DisplayObject3D {
		
		public static const WIDTH:int = 100;
		public var characterId:int = -1;
		public var className:String = "";
		
		public var main:Main;

		public var targetChar:Character;
		public var attackingChar:Character;
		public var currentDamageToDeal:int;
		
		public var token:Cube;
		public var outline:GlowFilter;
		
		public var hurting:Boolean = false;
		public var hurtingTime:uint = 0;
		
		public var oldPos:Number3D;
		
		public var ball:HomingBall;
	
		public var attackingOldPos:Number3D;
		public var meleeAttacking:Boolean;
		public var meleeAttackingTime:int = 0;
		public var drawingBackTime:int = -1;
		public var chargingTime:int = -1;
		public var returningTime:int = -1;
		
		public var drawingBackDist:uint = 20;
		public var drawingBackLength:uint = 4 * 2;
		public var chargingLength:uint = 3 * 2;
		public var returningLength:uint = 8 * 2;
		
		public var castingSprites:Array;
		public var castingSpritesTime:int = -1;
		
		public var dead:Boolean;
	
		public function Character (iconName, x, z) {
			this.x = x;
			this.z = z;
			
			this.castingSprites = [];
			
			var iconObj:Loader = new Loader();
			if (iconName == "")
				iconName = "face";
				
			iconObj.load(new URLRequest("http://almasytactics.com/img/sprites/" + iconName + ".png"));
			iconObj.contentLoaderInfo.addEventListener(Event.COMPLETE, LoadComplete);
		}
		
		private function LoadComplete (evt:Event) {
			evt.target.removeEventListener(Event.COMPLETE, LoadComplete);
			var bitmap:Bitmap = Bitmap(evt.target.loader.content);
			
			var iconMat:BitmapMaterial = new BitmapMaterial(bitmap.bitmapData);
			iconMat.smooth = true;
			
			var colorMat:ColorMaterial = new ColorMaterial(0xCCCCCC);
			
			var matList:MaterialsList = new MaterialsList();
			matList.addMaterial(colorMat, "front");
			matList.addMaterial(colorMat, "back");
			matList.addMaterial(colorMat, "right");
			matList.addMaterial(colorMat, "left");
			matList.addMaterial(iconMat, "top");
			matList.addMaterial(colorMat, "bottom");
			
			token = new Cube(matList, WIDTH, WIDTH, 10);
			token.useOwnContainer = true;
			
			outline = new GlowFilter(0x000000, 1.0, 4.0, 4.0, 4);
			token.filters = [outline];
			
			this.addChild(token);
		}
		
		public function Die () {
			dead = true;
		}
		
		private function EndAttackAnimation () {
			main.OnAnimationFinish();
		}
		
		public function BeginAttackAnimation (targetChar, damage) {			
			this.currentDamageToDeal = damage;
			this.targetChar = targetChar;
			
			if (Math.random() > 0.9)
				BeginMeleeAttack();
			else
				BeginMagicAttack();
		}
		
		public function BeginMagicAttack () {
			this.castingSpritesTime = 0;
			for (var angle:uint = 0; angle < 360; angle += 40) {
				var x = this.x + Math.cos(angle * Math.PI / 180) * 100;
				var z = this.z + Math.sin(angle * Math.PI / 180) * 100;
				var sprite:CastingSprite = new CastingSprite(x, this.y + 30, z, this.x, this.y + 20, this.z);
				sprite.main = this.main;
				sprite.color = 0x6046AA;
				castingSprites.push(sprite);
				this.scene.addChild(sprite);
				sprite.Initialize();
			}
		}
		
		public function EndCastingAnimation () {
			for (var i:uint = 0; i < castingSprites.length; i++)
				(castingSprites[i] as CastingSprite).Remove();
			castingSprites = [];
		
		
			ReleaseBall();
		}
		
		public function ReleaseBall () {
			var start:Number3D = new Number3D(this.x, this.y + 20, this.z);
			var end:Number3D = new Number3D(targetChar.x, targetChar.y, targetChar.z);
			
			var ball:HomingBall = new HomingBall(start, end, this, HitTarget);
			ball.vY = 5;
			ball.color = 0x40158C;
			ball.trailColor = 0x40158C;
			ball.main = this.main;
			
			this.scene.addChild(ball);
			ball.Initialize();
			this.ball = ball;
		}
		
		public function BeginMeleeAttack () {
			this.meleeAttacking = true;
			this.meleeAttackingTime = 0;
			this.drawingBackTime = 0;
			this.attackingOldPos = new Number3D(this.x, this.y, this.z);
		}
		
		public function HitTarget () {
			targetChar.BeginHurtAnimation(this, this.currentDamageToDeal);
			EndAttackAnimation();
		}
		
		public function EndHurtAnimation () {
			this.x = this.oldPos.x;
			this.hurting = false;
			outline.color = 0x000000;
			outline.blurX = 4.0;
			outline.blurY = 4.0;
		}
		
		private function SetHurtOutline () {
			outline.color = 0x660000;
			outline.blurX = 10;
			outline.blurY = 10;
		}
		
		public function BeginHurtAnimation (attackingCharacter, damage) {
			this.attackingChar = attackingCharacter;
			
			this.hurting = true;
			this.hurtingTime = 0;
			
			SetHurtOutline();
			this.oldPos = new Number3D(this.x, this.y, this.z);
		}
		
		public function Update () {
			if (this.ball != null)
				this.ball.Update();
				
			for (var i:uint = 0; i < castingSprites.length; i++)
				(castingSprites[i] as CastingSprite).Update();
				
			if (this.hurting) {
				hurtingTime++;
				
				var dir = this.x > attackingChar.x ? 1 : -1;
				
				this.x = this.oldPos.x + Math.cos(hurtingTime * 2) * 10;
				
				if (hurtingTime > 15) {
					EndHurtAnimation();
				}
			}
			
			if (this.castingSpritesTime >= 0) {
				this.castingSpritesTime++;
				
				if (this.castingSpritesTime >= 17) {
					this.castingSpritesTime = -1;
					EndCastingAnimation();
				}
			}
			
			if (this.dead) {
				token.alpha = 0.5;
				SetHurtOutline();
			}
			
			if (this.meleeAttacking) {
				this.meleeAttackingTime++;

				var dirX:Number = targetChar.x - this.x;
				var dirY:Number = targetChar.y - this.y;
				var dirZ:Number = targetChar.z - this.z;

				var length = Math.sqrt(dirX * dirX + dirY * dirY + dirZ * dirZ);

				dirX /= length;
				dirY /= length;
				dirZ /= length;
				
				var chargeX = targetChar.x - this.attackingOldPos.x;
				var chargeY = targetChar.y - this.attackingOldPos.y;
				var chargeZ = targetChar.z - this.attackingOldPos.z;

				var chargeDist = Math.sqrt(chargeX * chargeX + chargeY * chargeY + chargeZ * chargeZ);
					
				if (this.drawingBackTime >= 0) {
					this.drawingBackTime++;
					
					this.x += dirX * -1 * (drawingBackDist / drawingBackLength);
					this.y += dirY * -1 * (drawingBackDist / drawingBackLength);
					this.z += dirZ * -1 * (drawingBackDist / drawingBackLength);
					
					if (this.drawingBackTime >= drawingBackLength) {
						this.chargingTime = 0;
						this.drawingBackTime = -1;
					}
				} else if (this.chargingTime >= 0) {					
					this.chargingTime++;
					
					this.x += dirX * ((chargeDist - 25) / chargingLength);
					this.y += dirY * ((chargeDist - 25) / chargingLength);
					this.z += dirZ * ((chargeDist - 25) / chargingLength);
					
					if (this.chargingTime >= chargingLength) {
						targetChar.BeginHurtAnimation(this, this.currentDamageToDeal);
						this.returningTime = 0;
						this.chargingTime = -1;
					}
				} else if (this.returningTime >= 0) {
					this.returningTime++;
					
					this.x += dirX * -1 * (chargeDist / returningLength);
					this.y += dirY * -1 * (chargeDist / returningLength);
					this.z += dirZ * -1 * (chargeDist / returningLength);
					
					if (this.returningTime >= returningLength) {
						this.x = this.attackingOldPos.x;
						this.y = this.attackingOldPos.y;
						this.z = this.attackingOldPos.z;
						
						this.returningTime = -1;
						this.meleeAttacking = false;
						
						EndAttackAnimation();
					}
				}
				
			}
		}
	}
}