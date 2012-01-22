package com.almasy {
	import flash.display.*;
	import flash.text.*;
	import flash.net.*;
	
	public class StatusBarCharacter extends Sprite {
		public var hp:Number;
		public var maxHp:Number;
		public var characterId:uint;
		
		public var hpText:TextField;
		public var charText:TextField;
		public var iconObj:Loader;
		
		public var crossOut:Sprite;
		
		public function StatusBarCharacter (charName, icon, x, y) {
			this.hp = 0;
			this.maxHp = 0;
			
			this.x = x;
			this.y = y;
			
			var format:TextFormat = new TextFormat();
			format.font = "Trebuchet MS";
			format.size = 12;
			
			hpText = new TextField();
			hpText.y = 13;
			hpText.defaultTextFormat = format;
			//this.addChild(hpText);
			
			charText = new TextField();
			charText.defaultTextFormat = format;
			charText.text = charName;
			charText.autoSize = "center";
			charText.y = 50;
			charText.x = (50 - charText.width) / 2;
			this.addChild(charText);
			
			iconObj = new Loader();
			if (icon == "")
				icon = "face";
			iconObj.load(new URLRequest("http://almasytactics.com/img/sprites/" + icon + ".png"));
			iconObj.scaleX = 0.5;
			iconObj.scaleY = 0.5;
			
			var border:Sprite = new Sprite();
			border.graphics.lineStyle(1.0, 0x000000, 1.0, true);
			border.graphics.beginFill(0x000000);
			border.graphics.drawRect(-1, -1, 51, 51);
			border.graphics.endFill();
			border.addChild(iconObj);
			
			this.addChild(border);
			
			crossOut = new Sprite();
			crossOut.graphics.lineStyle(3.0, 0x660000, 1.0, true);
			crossOut.graphics.moveTo(-2, -2);
			crossOut.graphics.lineTo(52, 52);
			crossOut.graphics.moveTo(52, -2);
			crossOut.graphics.lineTo(-2, 52);
			crossOut.visible = false;
			this.addChild(crossOut);
			
			Update();
		}
		
		public function Update () {
			hpText.text = hp + " / " + maxHp;
			
			var percent:Number = maxHp != 0 ? hp / maxHp : 1;
									
			this.graphics.clear();
			
			var width:int = 50;

			// Draw border
			this.graphics.lineStyle(1.0, 0x000000, 1.0, true);
			this.graphics.beginFill(0x000066);
			this.graphics.drawRect(52, 0, 4, width);
			this.graphics.endFill();

			// Draw HP element			
			var fillColor:uint = 0;
			if (percent > .6) {
				fillColor = 0x00FF00;
			} else if (percent > .25) {
				fillColor = 0xFFFF00;
			} else {
				fillColor = 0xFF0000;
			}
			this.graphics.lineStyle(1.0, 0x000000, 1.0, true);
			this.graphics.beginFill(fillColor);
			this.graphics.drawRect(52, width * (1 - percent), 4, width * percent);
			this.graphics.endFill();
			
			// Draw crosses if dead
			crossOut.visible = (this.hp <= 0);
		}
	}
}