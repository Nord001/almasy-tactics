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
	
	public class Projectile extends DisplayObject3D {
		public var main:Main;
		
		public var color:uint;
		public var trailColor:int = -1;
		
		public var vX:Number = 0;
		public var vY:Number = 0;
		public var vZ:Number = 0;
		
		public var trail:MotionTrail;
	
		public function Projectile () {
		}
		
		public function Initialize () {
			var add :BitmapData = new BitmapData(64, 64, true, 0);
			var mat:Matrix = new Matrix();
			mat.createGradientBox(64, 64);
			
			var s:Sprite = new Sprite();
			s.graphics.beginGradientFill(GradientType.RADIAL, [color, color], [1, 0], [0x99, 0xFF], mat);
			s.graphics.drawRect(0, 0, 64, 64);
			s.graphics.endFill();
			add.draw(s);
			
			var pp:Plane = new Plane(new MovieMaterial(s, true));
			
			this.addChild(pp);
	
			var useTrailColor = trailColor == -1 ? color : trailColor;
			
			trail = new MotionTrail(MotionTrail.createGradientMovie(useTrailColor, useTrailColor), this, 6, 20, 400, 0.01);
			this.scene.addChild(trail);
		}
		
		public function Remove () {
			this.scene.removeChild(trail);
			this.scene.removeChild(this);
		}
		
		public function Update () {
			this.lookAt(main.camera);
			this.yaw(180);
			
			this.x += vX;
			this.y += vY;
			this.z += vZ;
		}
	}
}