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

			#position, #lap, #minimap, #speed {
				position: absolute;
			}

			#position {
				top: 32px;
				left: 32px;
			}

			#lap {
				top: 32px;
				right: 32px;
			}

			#minimap {
				left: 32px;
				bottom: 32px;
			}

			#speed {
				right: 32px;
				bottom: 32px;
			}

			section {
				margin: 4px;
				padding: 8px;
				width: 224px;
				font-family: "Open Sans";
				font-weight: bolder;
				text-transform: uppercase;
				background: rgba(0, 0, 0, 0.6);
			}

			section p {
				color: #FFF;
			}
		</style>
	</head>
	<body>
		<div id="position">
			<section>
				<p>Arya Stark</p>
			</section>
			<section>
				<p>Ned Stark</p>
			</section>
			<section>
				<p>Bran Stark</p>
			</section>
		</div>
		<div id="lap">
			<section>
				<p>Best 1:40:749</p>
			</section>
			<section>
				<p>Current 1:25:968</p>
			</section>
		</div>
		<div id="minimap">
			<!-- EMPTY -->
		</div>
		<div id="speed">
			<section>
				<p>146 km/h</p>
			</section>
		</div>

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
			hemiLight = new THREE.HemisphereLight( 0x0000ff, 0x00ff00, 1 );
			hemiLight.color.setHSL( 0.6, 0.6, 0.6 );
			hemiLight.groundColor.setHSL( 0.1, 1, 0.75 );
			hemiLight.position.set( 0, 500, 0 );
			scene.add( hemiLight );

			//Creating the sky
			//Not yet implemented

			//Creating the grass
			var grassWidth = 3200 * 6;
			var grassHeight = 3200 * 6;
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

			var carSpeed = 0.0;
			var maxSpeed = 20.0;
			var maxReverseSpeed = -10.0;

			var keyPressed = false;

			loader.load("./models/car.js", function(geometry) {
				var material = new THREE.MeshLambertMaterial({
					map: THREE.ImageUtils.loadTexture("./models/textures/car.jpg"),
					colorAmbient: [0.480000026226044, 0.480000026226044, 0.480000026226044],
					colorDiffuse: [0.480000026226044, 0.480000026226044, 0.480000026226044],
					colorSpecular: [0.8999999761581421, 0.8999999761581421, 0.8999999761581421]
				});

				car = new THREE.Mesh(geometry, material);

				camera.position.set(0, 300, -400);

				car.add(camera);
				scene.add(car);
				render();
			});

			function render() {
				renderer.render(scene, camera);

				//Camera for testing purposes
				/*camera.position.set(car.position.x, car.position.y + 40, car.position.z - 400);
				camera.lookAt(new THREE.Vector3(car.position.x, car.position.y + 100, car.position.z));*/

				//Normal camera
/*				camera.position.set(car.position.x, car.position.y + 300, car.position.z - 400);
				camera.lookAt(new THREE.Vector3(car.position.x, car.position.y + 100, car.position.z));*/


				if (typeof car == "object") {

					car.translateZ(carSpeed);

					if(keyPressed == false) {

						carSpeed = lerp(carSpeed, 0, 0.01);
					}
				}

				camera.lookAt( car.position );
				requestAnimationFrame(render);
			}

			document.onkeypress = function(event) {


				event.preventDefault();
				event.stopPropagation();


				console.log(event.which)

				switch (event.which) {

					case 119:
						keyPressed = true;

						if (carSpeed < -0.5) {

							carSpeed = lerp(carSpeed, 0, 0.05);
						} else {

							carSpeed = lerp(carSpeed, maxSpeed, 0.05);
						}
						break;

					case 115:
						keyPressed = true;

						if (carSpeed > 0.5) {

							carSpeed = lerp(carSpeed, 0, 0.05);
						} else {

							carSpeed = lerp(carSpeed, maxReverseSpeed, 0.05);
						}
						break;

					case 32:

						carSpeed = lerp(carSpeed, 0, 0.15);
						break;

					case 97:

						car.rotation.y += 0.03;
						break;

					case 100:
						car.rotation.y -= 0.03;
						break;
				}
			}

			document.onkeyup = function(event) {

				keyPressed = false;
			}

			lerp = function(a, b, u) {
				return (1 - u) * a + u * b;
			};
		</script>
	</body>
</html>
