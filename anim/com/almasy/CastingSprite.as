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
	
	public class CastingSprite extends DisplayObject3D {
		public var main:Main;
		
		public var color:uint;
		
		public var targetX:Number = 0;
		public var targetY:Number = 0;
		public var targetZ:Number = 0;
		
		public var trail:MotionTrail;
		
		public var speed:Number = 6;
	
		public function CastingSprite (x, y, z, targetX, targetY, targetZ) {
			this.x = x;
			this.y = y;
			this.z = z;
			
			this.targetX = targetX;
			this.targetY = targetY;
			this.targetZ = targetZ;
		}
		
		public function Initialize () {
			var pp:Plane = new Plane(new ColorMaterial(color), 17, 17);
			pp.useOwnContainer = true;
			pp.alpha = 0.8;
			
			this.addChild(pp);
	
			trail = new MotionTrail(MotionTrail.createGradientMovie(color, color), this, 5, 17, 400, 0.01);
			this.scene.addChild(trail);
			
			var vpl:ViewportLayer = main.castingLayer;
			vpl.addDisplayObject3D(trail);
		}
		
		public function Remove () {
			this.scene.removeChild(trail);
			this.scene.removeChild(this);
			main.castingLayer.removeDisplayObject3D(trail);
		}
		
		public function Update () {
			this.lookAt(main.camera);
			this.yaw(180);
			
			var dirX:Number = targetX - this.x;
			var dirY:Number = targetY - this.y;
			var dirZ:Number = targetZ - this.z;
			
			var length = Math.sqrt(dirX * dirX + dirY * dirY + dirZ * dirZ);
			
			dirX /= length;
			dirY /= length;
			dirZ /= length;
			
			this.x += dirX * speed;
			this.y += dirY * speed;
			this.z += dirZ * speed;
		}
	}
}
