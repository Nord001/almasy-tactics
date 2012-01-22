package com.almasy {
	import flash.display.Sprite;

	public class StatusBar extends Sprite {
		public var characters:Array;
		
		public function StatusBar (x, y) {
			this.x = x;
			this.y = y;
			
			this.graphics.lineStyle(0,0xFF6600);
			this.graphics.beginFill(0xFF3300);
			this.graphics.drawRoundRect(0, 0, 390, 155, 10);
			this.graphics.endFill();
		}
		
//		public function Update (dt:Number) {
//			for (var i:uint = 0; i < this.numChildren; i++)
//				(this.getChildAt(i) as StatusBarCharacter).Update(dt);
//		}

		public function LoadData (data) {
			characters = [];
			
			var position = 0;
			for (var i:uint = 0; i < 12; i++) {
				if (data[i] == undefined)
					continue;
					
				var col = position % 4;
				var row = (int)(position / 4);
				position++;
				var character:StatusBarCharacter = new StatusBarCharacter(data[i].name, data[i].icon, col * 65 + 5, row * 75 + 5);
				character.characterId = data[i].id;
				character.hp = 45;
				character.maxHp = 100;
				character.Update();
				this.addChild(character);
				characters.push(character);
			}
		}
		
		public function GetStatusBarCharacterById (id) : StatusBarCharacter {
			for (var i:uint = 0; i < characters.length; i++) {
				var character = characters[i] as StatusBarCharacter;
				if (character.characterId == id)
					return character;
			}
			return null;
		}
	}
}