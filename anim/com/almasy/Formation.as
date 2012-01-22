package com.almasy {
	import flash.display.*;
	import flash.text.*;
	import flash.net.*;
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
	
	public class Formation extends DisplayObject3D {
		public static const ATTACKER:int = 0;
		public static const DEFENDER:int = 1;
		
		public static const FORMATION_SPACING:int = 150;

		public var main:Main;
		public var type:int;
		public var characters:Array;
	
		public function Formation (type) {
			this.type = type;
			if (this.type == ATTACKER)
				this.x = -FORMATION_SPACING;
			else if (this.type == DEFENDER)
				this.x = FORMATION_SPACING;
		}
		
		public function LoadData (data) {
			characters = new Array();
			
			for (var i:uint = 0; i < 12; i++) {
				if (data[i] == undefined)
					continue;
					
				var col = i % 4;
				var row = (int)(i / 4);
				
				// x is specified for formations as the back edge of the back row.
				var rowX;
				if (this.type == ATTACKER) {
					rowX = -Character.WIDTH / 2 - row * 130;
				} else {
					rowX = Character.WIDTH / 2 + row * 130;
				}
			
				var character:Character = new Character(data[i].icon, this.x + rowX, this.y + col * 130);
				character.main = this.main;
				character.characterId = data[i].id;
				character.className = data[i].className;
				this.scene.addChild(character);
				characters.push(character);
			}
		}
		
		public function Update () {
			for (var i:uint = 0; i < characters.length; i++) {
				var character = characters[i] as Character;
				character.Update();
			}
		}
		
		public function GetCharacterById (id) : Character {
			for (var i:uint = 0; i < characters.length; i++) {
				var character = characters[i] as Character;
				if (character.characterId == id)
					return character;
			}
			return null;
		}
	}
}