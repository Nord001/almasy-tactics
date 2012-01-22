package com.almasy {
	import flash.display.*;
	import flash.text.*;
	import flash.net.*;
	
	public class SplashScreen extends Sprite {
	
		public var t:uint = 0;
	
		public function SplashScreen (battleData) {
		}
		
		public function Update () {
			if (!this.visible)
				return;
				
			t++;
		}
	}
}