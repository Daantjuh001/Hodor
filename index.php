<!DOCTYPE html>

<html>
	<head>
		<title>Game</title>
		<style>
			* {
				margin: 0px;
				padding: 0px;
			}

			canvas {
				display: block;
				width: 100%;
				height: 100%;
			}
		</style>
	</head>
	<body>
		<script src="https://rawgithub.com/mrdoob/three.js/master/build/three.js"></script>
		<script>
			//Setting up variables
			var windowWidth = window.innerWidth;
			var windowHeight = window.innerHeight;
			
			//Setting up THREE JS
			var scene = new THREE.Scene();
			var camera = new THREE.PerspectiveCamera(45, windowWidth / windowHeight, 0.1, 20000);
			var renderer = new THREE.WebGLRenderer({ antialias: true });

			//Making sure the camera object works
			scene.add(camera);

			//Making sure the renderer object works
			renderer.setSize(windowWidth, windowHeight);
			document.body.appendChild(renderer.domElement);

			//Adding light 
			var light = new THREE.SpotLight();
			light.position.set(0, 500, 0);
			scene.add(light);

			//Creating the sky
			//Not yet implemented

			//Creating the grass
			var grassWidth = 3200;
			var grassHeight = 3200;
			var grassTexture = THREE.ImageUtils.loadTexture("./models/textures/grass.jpg");
			grassTexture.wrapS = THREE.RepeatWrapping;
			grassTexture.wrapT = THREE.RepeatWrapping;
			grassTexture.repeat.set(grassWidth / 256, grassHeight / 256);
			var grass = new THREE.Mesh(new THREE.PlaneGeometry(grassWidth, grassHeight), new THREE.MeshBasicMaterial({ map: grassTexture }));
			grass.rotation.x = - Math.PI / 2;
			grass.doubleSided = true;
			scene.add(grass);

			//Loading the car object
			var loader = new THREE.JSONLoader();

			var car;
			loader.load("./models/car.js", function(geometry) {
				var material = new THREE.MeshLambertMaterial({
					map: THREE.ImageUtils.loadTexture("./models/textures/car.jpg"),
					colorAmbient: [0.480000026226044, 0.480000026226044, 0.480000026226044],
					colorDiffuse: [0.480000026226044, 0.480000026226044, 0.480000026226044],
					colorSpecular: [0.8999999761581421, 0.8999999761581421, 0.8999999761581421]
				});

				car = new THREE.Mesh(geometry, material);
				scene.add(car);
				render();
			});

			function render() {
				renderer.render(scene, camera);

				//Camera for testing purposes
				/*camera.position.set(car.position.x, car.position.y + 40, car.position.z - 400);
				camera.lookAt(new THREE.Vector3(car.position.x, car.position.y + 100, car.position.z));*/

				//Normal camera
				camera.position.set(car.position.x, car.position.y + 300, car.position.z - 400);
				camera.lookAt(new THREE.Vector3(car.position.x, car.position.y + 100, car.position.z));

				requestAnimationFrame(render);
			}
		</script>
	</body>
</html>