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

		<script src="three.js"></script>
        <script src="jquery.js"></script>
		<script>
			//Setting up variables
			var windowWidth = window.innerWidth;
			var windowHeight = window.innerHeight;

			//Setting up THREE JS
			var scene = new THREE.Scene();
			var camera = new THREE.PerspectiveCamera(45, windowWidth / windowHeight, 0.1, 20000);
			var renderer = new THREE.WebGLRenderer({ antialias: true });
			var clock = new THREE.Clock();

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
            var imagePrefix = "images/skybox-";
            var directions  = ["xpos", "xneg", "ypos", "yneg", "zpos", "zneg"];
            var imageSuffix = ".png";
            var skyGeometry = new THREE.CubeGeometry( 19500, 19500, 19500 );

            var materialArray = [];
            for (var i = 0; i < 6; i++)
                materialArray.push( new THREE.MeshBasicMaterial({
                    map: THREE.ImageUtils.loadTexture( imagePrefix + directions[i] + imageSuffix ),
                    side: THREE.BackSide
                }));
            var skyMaterial = new THREE.MeshFaceMaterial( materialArray );
            var skyBox = new THREE.Mesh( skyGeometry, skyMaterial );
            scene.add( skyBox );

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

			//Creating the track
			var geometry = new THREE.RingGeometry(4000, 3000, 8, 8, 0, Math.PI * 2);
			var ring = new THREE.Mesh(geometry, new THREE.MeshBasicMaterial({color: 0x3B3B3B, wireframe: false}));
			ring.position.set(0, 2, 0);
			ring.rotation.x = Math.PI / 2;
			scene.add(ring);

			//Loading the car object
			var loader = new THREE.JSONLoader();

			var car;

			var carSpeed = 0.0;
			var maxSpeed = 60.0;
			var maxReverseSpeed = -10.0;

            var turnSpeed = 0.015;
            var accelerateSpeed = 0.025;
            var handBrakeSpeed = 0.01;

			loader.load("./models/car.js", function(geometry) {
				var material = new THREE.MeshLambertMaterial({
					map: THREE.ImageUtils.loadTexture("./models/textures/car.jpg"),
					colorAmbient: [0.480000026226044, 0.480000026226044, 0.480000026226044],
					colorDiffuse: [0.480000026226044, 0.480000026226044, 0.480000026226044],
					colorSpecular: [0.8999999761581421, 0.8999999761581421, 0.8999999761581421]
				});

				car = new THREE.Mesh(geometry, material);
				car.position.set(3250, 0, 0);

				camera.position.set(0, 0, -400);

				scene.add(car);
				render();
			});

			// Creating the smoke
			var smokeParticles = new THREE.Geometry;
			for (var i = 0; i < 25; i++) {
			    var particle = new THREE.Vector3(0, 300, -400);
			    smokeParticles.vertices.push(particle);
			}

			var smokeTexture = THREE.ImageUtils.loadTexture('images/particle.png');
			var smokeMaterial = new THREE.ParticleBasicMaterial({ map: smokeTexture, transparent: true, blending: THREE.AdditiveBlending, size: 50, color: 0x111111 });

			var smoke = new THREE.ParticleSystem(smokeParticles, smokeMaterial);
			smoke.sortParticles = true;
			smoke.position.x = -150;

			scene.add(smoke);

			function render() {
				renderer.render(scene, camera);
				var delta = clock.getDelta();

				// Moving the car
				if (typeof car == "object") {

					car.translateZ(carSpeed);

                    $.each(map, function( index, value ) {

                        if(value === true) {

                            if(index == 87) {
                                carSpeed = lerp(carSpeed, maxSpeed, delta);
                            } else if(index == 83) {

                                carSpeed = lerp(carSpeed, maxReverseSpeed, delta);
                            }

                            if(index == 65) {

                                car.rotateOnAxis( new THREE.Vector3(0,1,0), turnSpeed);
                            } else if(index == 68) {

                                car.rotateOnAxis( new THREE.Vector3(0,1,0), -turnSpeed);
                            }

                        } else {

                            carSpeed = lerp(carSpeed, 0, delta);
                        }
                    });

                    // Make sure the smoke goes up and stays behind the car
					var particleCount = smokeParticles.vertices.length;
					while (particleCount--) {
					    var particle = smokeParticles.vertices[particleCount];
					    particle.y += delta * 50;

					    if (particle.y >= 25) {
					        particle.y = Math.random() * 16;
					        particle.x = Math.random() * 5;
					        particle.z = Math.random() * 5;
					    }
					}
					smokeParticles.__dirtyVertices = true;


					var relativeSmokeOffset = new THREE.Vector3(-22, 13, -115);

					var smokeOffset = relativeSmokeOffset.applyMatrix4( car.matrixWorld );

					smoke.position.x = smokeOffset.x;
					smoke.position.y = smokeOffset.y;
					smoke.position.z = smokeOffset.z;
					// console.log(smoke.position)
				}


				var relativeCameraOffset = new THREE.Vector3(0,200,-500);

				var cameraOffset = relativeCameraOffset.applyMatrix4( car.matrixWorld );

				camera.position.x = cameraOffset.x;
				camera.position.y = cameraOffset.y;
				camera.position.z = cameraOffset.z;
				camera.lookAt( car.position );
				requestAnimationFrame(render);
			}

            var map = {87: false, 65: false, 83: false, 68: false, 32: false};
            $(document).keydown(function(e) {
                if (e.keyCode in map) {
                    map[e.keyCode] = true;
                }
            }).keyup(function(e) {
                if (e.keyCode in map) {
                    map[e.keyCode] = false;
                }
            });

			lerp = function(a, b, u) {
				return (1 - u) * a + u * b;
			};
		</script>
	</body>
</html>
