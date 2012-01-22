package com.almasy {
	import flash.display.*;
	import flash.text.*;
	import flash.net.*;
	import flash.events.*;
	import flash.display.*;
	import flash.filters.*;
	import flash.geom.Matrix;
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
	import org.papervision3d.view.layer.ViewportLayer;
	
	public class HomingBall extends Projectile {
		
		public var character:Character;
		public var animationFinishHandler:Function;
		
		public var start:Number3D;
		public var end:Number3D;
		
		public var speed:Number = 10;
	
		public function HomingBall (start, end, character, finishHandler) {
			super();
			this.start = start;
			this.end = end;
			this.character = character;
			this.animationFinishHandler = finishHandler;
			this.x = start.x;
			this.y = start.y;
			this.z = start.z;
		}
		
		public override function Update () {
			var dirX:Number = end.x - this.x;
			var dirY:Number = end.y - this.y;
			var dirZ:Number = end.z - this.z;
			
			var length = Math.sqrt(dirX * dirX + dirY * dirY + dirZ * dirZ);
			
			var moveSpeed = Math.sqrt(this.vX * this.vX + this.vY * this.vY + this.vZ * this.vZ);
			
			if (length - moveSpeed < 3) {
				Remove();
				character.ball = null;
				animationFinishHandler.apply(main);
				return;
			}
			
			dirX /= length;
			dirY /= length;
			dirZ /= length;
			
			this.vX += dirX * speed;
			this.vY += dirY * speed;
			this.vZ += dirZ * speed;
			
			super.Update();
		}
	}
}