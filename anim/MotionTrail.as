package
{
	import flash.display.GradientType;
	import flash.display.Sprite;
	import flash.geom.Matrix;
	
	import org.papervision3d.core.geom.TriangleMesh3D;
	import org.papervision3d.core.geom.renderables.Triangle3D;
	import org.papervision3d.core.geom.renderables.Vertex3D;
	import org.papervision3d.core.material.TriangleMaterial;
	import org.papervision3d.core.math.Number3D;
	import org.papervision3d.core.math.NumberUV;
	import org.papervision3d.core.render.data.RenderSessionData;
	import org.papervision3d.materials.MovieMaterial;
	import org.papervision3d.objects.DisplayObject3D;
	import org.papervision3d.objects.primitives.Sphere;

	
	public class MotionTrail extends TriangleMesh3D
	{
		
		private var iterations : Number = 10;
		private var target : DisplayObject3D;
		private var nodes : Array = [];
		public var maxWidth : Number = 40;
		public var maxSpeed : Number = 40;
		private var testViz : Array = [];
		public var  minEase  : Number = 0.15;
		
		public function MotionTrail(material:TriangleMaterial, target: DisplayObject3D, iterations:Number = 10, maxWidth:Number = 40, maxSpeed : Number = 40, minEase : Number = 0.05)
		{
			super(material, null, null, null);
			this.target = target;
			this.iterations = iterations;	
			material.doubleSided = true;
			
			this.maxWidth = maxWidth;
			this.maxSpeed = maxSpeed * maxSpeed;
			this.minEase = minEase;
			
			buildTrailMesh();
				
		}
		
		public static function createGradientMovie(color1:uint = 0xFFeF30, color2:uint = 0xFFeF30):MovieMaterial{
			
			var mat:Matrix = new Matrix();
			mat.createGradientBox(256, 256);
			
			var s:Sprite = new Sprite();
			s.graphics.beginGradientFill(GradientType.LINEAR, [color1, color2], [1, 0], [0x66, 0xFF], mat);
			s.graphics.drawRect(0, 0, 256, 256);
			s.graphics.endFill();
			return  new MovieMaterial(s, true);
		}
		
		public override function project(parent:DisplayObject3D, renderSessionData:RenderSessionData):Number{
			updateTrail();
			return super.project(parent, renderSessionData);
		}
		
		private function updateTrail():void{
			if(!target)
				return;
				
			chase(new Number3D(target.x, target.y, target.z), nodes[0]);
			
			
			for(var i:int = 1;i<iterations;i++){
				chase(nodes[i-1], nodes[i]);

			}
			
			updateMesh();
			
			return;
		}
		
		private function updateMesh():void{
			
			var cNode : TrailNode;
			var v1: Vertex3D;
			var v2: Vertex3D;
			var tSpeed : Number;
			
			for(var i:int = 0;i<iterations;i++){
				cNode = nodes[i];
				v1 = this.geometry.vertices[i*2];
				v2 = this.geometry.vertices[i*2+1];
				cNode.nodeVector.reset(view.n12, view.n22, view.n32);
				tSpeed = (/* cNode.chaseSpeed/maxSpeed* */(1-i/iterations))*maxWidth;
				v1.x = cNode.x + cNode.nodeVector.x*tSpeed;
				v1.y = cNode.y + cNode.nodeVector.y*tSpeed;
				v1.z = cNode.z + cNode.nodeVector.z*tSpeed;
				v2.x = cNode.x - cNode.nodeVector.x*tSpeed;
				v2.y = cNode.y - cNode.nodeVector.y*tSpeed;
				v2.z = cNode.z - cNode.nodeVector.z*tSpeed;

			}
		}
		
		
		private function chase(node1:Number3D, node2:TrailNode):void{
			node2.dx = (node1.x-node2.x)*node2.spring;
			node2.dy = (node1.y-node2.y)*node2.spring;
			node2.dz = (node1.z-node2.z)*node2.spring;
			node2.updateNode();
		}
			
		private function buildTrailMesh():void{
			var gridX    :Number = iterations-1;
			var gridY    :Number = 1;
			var gridX1   :Number = gridX + 1;
			var gridY1   :Number = gridY + 1;
	
			var vertices :Array  = this.geometry.vertices;
			var faces    :Array  = this.geometry.faces;
	
			var textureX :Number = 1;
			var textureY :Number = 1;
	
			var iW       :Number = 1 / gridX;
			var iH       :Number = 1 / gridY;
	
			// Vertices
			for( var ix:int = 0; ix < gridX + 1; ix++ )
			{
				for( var iy:int = 0; iy < gridY1; iy++ )
				{
					var x :Number = ix * iW - textureX;
					var y :Number = iy * iH - textureY;
	
					vertices.push( new Vertex3D( x, y, 0 ) );
				}
			}
	
			// Faces
			var uvA :NumberUV;
			var uvC :NumberUV;
			var uvB :NumberUV;
	
			for(  ix = 0; ix < gridX; ix++ )
			{
				for(  iy= 0; iy < gridY; iy++ )
				{
					// Triangle A
					var a:Vertex3D = vertices[ ix     * gridY1 + iy     ];
					var c:Vertex3D = vertices[ ix     * gridY1 + (iy+1) ];
					var b:Vertex3D = vertices[ (ix+1) * gridY1 + iy     ];
	
					uvA =  new NumberUV( ix     / gridX, iy     / gridY );
					uvC =  new NumberUV( ix     / gridX, (iy+1) / gridY );
					uvB =  new NumberUV( (ix+1) / gridX, iy     / gridY );
	
					faces.push(new Triangle3D(this, [ a, b, c ], material, [ uvA, uvB, uvC ] ) );
	
					// Triangle B
					a = vertices[ (ix+1) * gridY1 + (iy+1) ];
					c = vertices[ (ix+1) * gridY1 + iy     ];
					b = vertices[ ix     * gridY1 + (iy+1) ];
	
					uvA =  new NumberUV( (ix+1) / gridX, (iy+1) / gridY );
					uvC =  new NumberUV( (ix+1) / gridX, iy      / gridY );
					uvB =  new NumberUV( ix      / gridX, (iy+1) / gridY );
					
					faces.push(new Triangle3D(this, [ a, b, c ], material, [ uvA, uvB, uvC ] ) );
				}
			}

			//build the nodes
			for(var i : int = 0;i<iterations;i++){
				
				nodes.push(new TrailNode(target.x, target.y, target.z, ((iterations-i)/iterations * (1-minEase)) + minEase));
				testViz.push(new Sphere(null, 20, 2, 2));
			}
			
			this.geometry.ready = true;
		}
		
	}
}

import org.papervision3d.core.math.Number3D;
	

internal class TrailNode extends Number3D{
	
	public var chaseSpeed : Number = 20;
	public var spring : Number = 1;
	public var nodeVector : Number3D = new Number3D(0, 1, 0);
	public var dx : Number;
	public var dy : Number;
	public var dz : Number;
	
	public function TrailNode(x:Number = 0, y:Number = 0, z:Number = 0, spring : Number = 1){
		super(x, y, z);
		this.spring = spring;
		
	}
	
	public function updateNode():void{
		chaseSpeed = dx*dx+dy*dy+dz*dz;
		x += dx;
		y += dy;
		z += dz;
	}
	
}