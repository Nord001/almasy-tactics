package{
	import flash.ui.*;
	import flash.events.*;
	import flash.display.*;
	import flash.utils.*;
	import flash.net.*;
	import flash.geom.*;
	import flash.filters.*;
	import flash.system.System;
	import org.papervision3d.cameras.Camera3D;
	import org.papervision3d.cameras.DebugCamera3D;
	import org.papervision3d.render.BasicRenderEngine;
	import org.papervision3d.scenes.Scene3D;
	import org.papervision3d.view.Viewport3D;
	import org.papervision3d.objects.primitives.*;
	import org.papervision3d.core.math.*;
	import org.papervision3d.materials.*;
	import com.adobe.serialization.json.JSON;
	import org.papervision3d.objects.*;
	import org.papervision3d.core.effects.*;
	import org.papervision3d.view.layer.*;
	import org.papervision3d.core.geom.*;
	import org.papervision3d.materials.special.*;
	import org.papervision3d.core.geom.renderables.Particle;
	import org.papervision3d.view.stats.StatsView;
	import org.papervision3d.view.layer.ViewportLayer;
	
	import com.almasy.*;

	public class Main extends flash.display.MovieClip {
		public var viewport:Viewport3D;
		private var scene:Scene3D;
		private var debugCamera:DebugCamera3D;
		public var camera:Camera3D;
		private var cameraTarget:DisplayObject3D;
		private var renderer:BasicRenderEngine;
		private var myLoader:URLLoader;
		private var messages:Array;

		private var attackingFormation:Formation;
		private var defendingFormation:Formation;
		private var attackerStatus:StatusBar;
		private var defenderStatus:StatusBar;
		
		private var curMessage:uint = 1;
		private var curMessageData:Object;
		
		public var castingLayer:ViewportLayer;
		
		public var splashScreen:SplashScreen;

        public function Main () {
			InitGraphics();
			LoadBattle();
			
			System.gc();
			var mem:String = Number( System.totalMemory / 1024 / 1024 ).toFixed( 2 ) + "Mb";
			trace("Starting Memory: " + mem);

			this.addEventListener(Event.ENTER_FRAME, RenderLoop);
        }
        
        private function Preprocess (message) : String {
        	var pattern:RegExp = /,}/g;
        	return message.replace(pattern, "}");
        }
		
		private function RenderLoop (evt:Event) {
			if (attackingFormation != null)	
				attackingFormation.Update();
				
			if (defendingFormation != null)	
				defendingFormation.Update();
				
			renderer.renderScene(scene, debugCamera, viewport);
		}
		
		private function LoadBattle () {
			myLoader = new URLLoader();
			myLoader.load(new URLRequest("data_new.txt"));
			myLoader.addEventListener(Event.COMPLETE, LoadComplete);
		}
		
		private function LoadComplete (evt:Event) {
			myLoader.removeEventListener(Event.COMPLETE, LoadComplete);
			
			messages = evt.target.data.split("\n");
		
			var battleData = JSON.decode(messages[0]);
			
			attackerStatus = new StatusBar(3, 3);
			attackerStatus.LoadData(battleData.attacker);
			addChild(attackerStatus);
			
			defenderStatus = new StatusBar(403, 3);
			defenderStatus.LoadData(battleData.defender);
			addChild(defenderStatus);
			
			attackingFormation = new Formation(Formation.ATTACKER);
			attackingFormation.main = this;
			scene.addChild(attackingFormation);
			attackingFormation.LoadData(battleData.attacker);
			
			defendingFormation = new Formation(Formation.DEFENDER);
			defendingFormation.main = this;
			scene.addChild(defendingFormation);
			defendingFormation.LoadData(battleData.defender);
			
			// Load starter hps
			curMessageData = JSON.decode(Preprocess(messages[1]));
			UpdateHps();
			
			// Display splash screen
			splashScreen = new SplashScreen(battleData);
			//scene.addChild(splashScreen);
			
			setTimeout(BeginFight, 3000);
		}
		
		private function BeginFight () {
			splashScreen.visible = false;
			ExecuteNextAnimation();
		}
		
		private function ExecuteNextAnimation () {
			while(true) {
				if (curMessage >= messages.length)
					return;
					
				var data = messages[curMessage];
				if (data.length == 0)
					return;
					
				curMessageData = JSON.decode(Preprocess(data));
				curMessage++;
					
				if (curMessageData.type == "attack")
					break;
			}
			
			var attackingChar = GetCharacterById(curMessageData.attackingCharId);
			var targetChar = GetCharacterById(curMessageData.targetCharId);
			
			if (attackingChar == null || targetChar == null) {
				trace("Error: attackingChar or targetChar was null");
				return;
			}
			
			attackingChar.BeginAttackAnimation(targetChar, 400);
		}
		
		public function OnAnimationFinish () {
			UpdateHps();
			setTimeout(ExecuteNextAnimation, 400);
		}
		
		public function UpdateHps () {
			for (var id in curMessageData.hps) {
				var characterData = curMessageData.hps[id];
				if (characterData == null)
					continue;
					
				var character = GetStatusBarCharacterById(id);
				if (character == null)
					continue;
					
				var split = characterData.split('/');
				
				character.hp = split[0];
				character.maxHp = split[1];
				character.Update();
				
				if (character.hp <= 0) {
					var characterToken = GetCharacterById(character.characterId);
					characterToken.Die();
				}
			}
		}
		
		private function GetStatusBarCharacterById (id) : StatusBarCharacter {
			var character = null;
			
			character = attackerStatus.GetStatusBarCharacterById(id);
			if (character != null)
				return character;
				
			character = defenderStatus.GetStatusBarCharacterById(id);
			if (character != null)
				return character;
				
			return null;
		}
		
		private function GetCharacterById (id) : Character {
			var character = null;
			
			character = attackingFormation.GetCharacterById(id);
			if (character != null)
				return character;
				
			character = defendingFormation.GetCharacterById(id);
			if (character != null)
				return character;
				
			return null;
		}
		
		private function InitGraphics () {
			viewport = new Viewport3D(800, 445);
			viewport.y = 155;
			addChild(viewport);
			
			scene = new Scene3D();
			
			cameraTarget = new PaperPlane();
			cameraTarget.z = 150;
			cameraTarget.y = 0;
			scene.addChild(cameraTarget);
			
			debugCamera = new DebugCamera3D(viewport, 45);
			debugCamera.x = 0;
			debugCamera.y = 400;
			debugCamera.z = 130;
			debugCamera.target = cameraTarget;
			
			camera = new Camera3D(45);
			camera.x = 0;
			camera.y = 500;
			camera.z = -250;			
			camera.target = cameraTarget;
			
			renderer = new BasicRenderEngine();
			var statsView:StatsView = new StatsView(renderer);
			statsView.y = 400;
			addChild(statsView);
			
			var background:Plane = new Plane(new ColorMaterial(0xBBAA66), 5000, 5000, 5);
			background.rotationX = 90;
			background.y = -500;
			scene.addChild(background);
			
			castingLayer = new ViewportLayer(viewport, null);
			castingLayer.blendMode = "subtract";
		}
   }
}