<?php

?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <style>
        #canvas {
            left: 100%;
            right: 100%;
            border: 1px solid #d3d3d3;
            background: linear-gradient(to bottom, #a3c6ff 0%, #ffffff 100%);
        }

        #gameArea {
            position: absolute;
            border: 1px solid #c15656;
            left: 50%;
            right: 50%;
        }

        .button {
            border: none;
            color: white;
            padding: 16px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            transition-duration: 0.4s;
            cursor: pointer;
        }

        .button1 {
            background-color: #FFFFE0;
            border-radius: 40px;
            color: black;
            border: 4px solid black;
            padding: 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            position: relative;
            left: 210px;
        }
	.button2{
          background-color: #FFFFE0;
          border-radius: 10px;
          color: black;
          border: 2px solid black;
          padding: 10px;
          text-align: center;
          text-decoration: none;
          display: inline-block;
          font-size: 10px;
          margin: 2px 1px;
          position: relative;
        }

        .button1:hover {
            background-color: #EE0000;
            color: white;
        }
        .button2:hover {
          background-color: #3a8a4b;
      		color: white;
        }
    </style>
</head>
<body onload="startGame()">
<div id="gameArea">
    <canvas id="canvas"></canvas>
    <script src="jquery.js"></script>
    <script type="text/javascript">
        var myGamePiece;
        var myObstacles = [];
        var scoreObstacles = [];
        var myScore;
        var myHighScore;
        var spacePressed;
        var jumpSound;
        var deathSound;
        var score = 0;
		var highscore = 0;
        var myName;
        var name = "";
        var nameQueued = "";
        var sfX;
        var sfY;
        var huskySprite;

        window.addEventListener('keydown', function (e) {
            if (e.keyCode === 32 && e.target === document.body) {
                e.preventDefault();
            }
        });
		
		function restartGame() {
			myGameArea.clear();
			myGamePiece = {};
			myObstacles = [];
			scoreObstacles = [];
			score = 0;
	        name = nameQueued;
			startGame();
		}
		
        function startGame() {
            myGameArea.resize();
            myGamePiece = new component(50 * sfX, 40 * sfY, "Huskypup.png", 25 * sfX, 120 * sfY, "image");
            myGamePiece.gravity = 0.10;
            myGamePiece.life = 1;
            myScore = new component("12px", "Verdana", "black", 500, 25, "text");
			myHighScore = new component("12px", "Verdana", "black", 477, 40, "text");
            myMessage = new component("12px", "Verdana", "black", 175, 235, "text");
	        myName = new component("12px", "Verdana", "black", 25, 25, "text");
            jumpSound = new sound("bark.mp3");
            deathSound = new sound("editedBonk.wav");
			
            myGameArea.start();
        }

        document.addEventListener("keydown", keyDownHandler, false);
        document.addEventListener("keyup", keyUpHandler, false);

        function keyDownHandler(e) {
            if (e.keyCode === 32) {
                spacePressed = true;
            }
        }

        function keyUpHandler(e) {
            if (e.keyCode === 32) {
                spacePressed = false;
            }
        }

        function setName() {
	    nameQueued = document.getElementById("name").value;
}

        var myGameArea = {
            canvas: document.getElementById("canvas"),
            gameArea: document.getElementById("gameArea"),
            ctx: this.canvas.getContext("2d"),
            start: function () {
                this.canvas.style = "border:3px solid black;";
                this.resize();
                this.context = this.ctx;
                this.frameNo = 0;
                updateGameArea();
            },
            resize: function () {
                this.context = this.ctx;
                var widthToHeight = 600 / 470;
                var newWidth = window.innerWidth - window.innerWidth * .25;
                var newHeight = window.innerHeight - window.innerHeight * .25;
                var newWidthToHeight = newWidth / newHeight;

                //keeps the game's aspect ratio
                if (newWidthToHeight > widthToHeight) {
                    newWidth = newHeight * widthToHeight;
                    this.gameArea.style.height = newHeight + 'px';
                    this.gameArea.style.width = newWidth + 'px';
                } else {
                    newHeight = newWidth / widthToHeight;
                    this.gameArea.style.width = newWidth + 'px';
                    this.gameArea.style.height = newHeight + 'px';
                }
                //keeps the gameArea in the center of the screen when resizing
                this.gameArea.style.marginLeft = (-newWidth / 2) + 'px';

                this.canvas.width = newWidth;
                this.canvas.height = newHeight;


                sfX = this.canvas.width / 600
                sfY = this.canvas.height / 470


            },
            clear: function () {
                this.context.clearRect(0, 0, this.canvas.width, this.canvas.height);
            }
        }
        window.addEventListener('resize', myGameArea.resize, false);
        window.addEventListener('fullscreenchange', myGameArea.resize, false)
        window.addEventListener('orientationchange', myGameArea.resize, false);

        function component(width, height, color, x, y, type) {
            this.type = type;
            this.score = 0;
            this.width = width;
            this.height = height;
            this.speedX = 0;
            this.speedY = 0;
            this.x = x;
            this.y = y;
            this.gravity = 0;
            this.gravitySpeed = 0;
            if (type === "image") {
                this.image = new Image();
                this.image.src = color;
            }
            this.update = function () {
                if (spacePressed) {
                    accelerate(-.4)
                    jumpSound.play();
                }
                if (!spacePressed) {
                    accelerate(.1)
                }
                ctx = myGameArea.context;


                if (this.type === "text") {
                    ctx.font = this.width * sfX + " " + this.height * sfY;
                    ctx.fillStyle = color;
                    ctx.fillText(this.text, this.x * sfX, this.y * sfY);
                }
                else if (type === "image") {
                    ctx.drawImage(this.image,
                        this.x,
                        this.y,
                        this.width, this.height);
                }
                else {
                    ctx.fillStyle = color;
                    ctx.fillRect(this.x, this.y, this.width, this.height);
                }
            }
            this.newPos = function () {
                this.gravitySpeed += this.gravity;
                this.x += this.speedX;
                this.y += this.speedY + this.gravitySpeed;
            }

            this.crashWith = function (otherobj) {
                var bottom = myGameArea.canvas.height - this.height;
                var top = 0
                var myleft = this.x;
                var myright = this.x + (this.width);
                var mytop = this.y;
                var mybottom = this.y + (this.height);
                var otherleft = otherobj.x;
                var otherright = otherobj.x + (otherobj.width);
                var othertop = otherobj.y;
                var otherbottom = otherobj.y + (otherobj.height);
                var crash = true;
                if (this.y > bottom || mytop < top) {
                    return true;
                }
                if ((mybottom < othertop) || (mytop > otherbottom) || (myright < otherleft) || (myleft > otherright)) {
                    crash = false;
                }
                return crash;
            }
        }

        function updateGameArea() {

            var x, height, gap, minHeight, maxHeight, minGap, maxGap;
            for (i = 0; i < myObstacles.length; i += 1) {
                if (myGamePiece.crashWith(myObstacles[i])) {
                    if (myGamePiece.life == 1) {
                        myGamePiece.life = 0;
                        deathSound.play();
                        myMessage.text = "You crashed! Press SPACE to try again!"
                        myMessage.update();
                    }
                    if (spacePressed) {
                        restartGame();
                    }
                    return;
                }
            }

            for (i = 0; i < scoreObstacles.length; i++) {
                if (myGamePiece.crashWith(scoreObstacles[i])) {
                    score++; 
                }
            }


            myGameArea.clear();
            myGameArea.frameNo += 1;
            if (myGameArea.frameNo === 1 || everyinterval(Math.round(150 * sfX))) {
                x = myGameArea.canvas.width;
                minHeight = 45;
                maxHeight = 225;
                height = Math.floor(Math.random() * (maxHeight - minHeight + 1) + minHeight);
                minGap = 90;
                maxGap = 150;
                gap = Math.floor(Math.random() * (maxGap - minGap + 1) + minGap);
		    
                myObstacles.push(new component(70 * sfX, height * sfY, "yellow", x, 0));
                myObstacles.push(new component(50 * sfX, height * sfY, "black", x, 0));
                myObstacles.push(new component(30 * sfX, height * sfY, "darkgrey", x, 0));
                myObstacles.push(new component(15 * sfX, height * sfY, "grey", x, 0));
                myObstacles.push(new component(90 *sfX , 20*sfY, "lightyellow", x - 10, height));

		    
                myObstacles.push(new component(70 * sfX, x - height - gap * sfY, "yellow", x, sfY * (height + gap)));
                myObstacles.push(new component(50 * sfX, x - height - gap * sfY, "black", x, sfY * (height + gap)));
                myObstacles.push(new component(30 * sfX, x - height - gap * sfY, "darkgrey", x, sfY * (height + gap)));
                myObstacles.push(new component(15 * sfX, x - height - gap * sfY, "grey", x, sfY * (height + gap)));
                myObstacles.push(new component(90 * sfX, 20*sfY, "lightyellow", x -10 ,  (height + gap)));

                scoreObstacles.push(new component(70 * sfX, gap * sfY, "#ff000000", x, sfY * height));
            }
            for (i = 0; i < myObstacles.length; i += 1) {
                myObstacles[i].x += -1.5;
                myObstacles[i].update();
            }
            for (i = 0; i, i < scoreObstacles.length; i++) {
                scoreObstacles[i].x += -1.5;
                scoreObstacles[i].update();
            }
            myScore.text = "SCORE: " + Math.round(score / (67 * sfX));
            myScore.update();
			if (score >= highscore) {
				highscore = score;

			}
			myHighScore.text="HIGH SCORE: " + Math.round(highscore/ (67 * sfX));;
			myHighScore.update();
	        myName.text = "Playing as: " + name
	        myName.update();
            myGamePiece.newPos();
            myGamePiece.update();
        }

        function sound(src) {
            this.sound = document.createElement("audio");
            this.sound.src = src;
            this.sound.setAttribute("preload", "auto");
            this.sound.setAttribute("controls", "none");
            this.sound.style.display = "none";
            document.body.appendChild(this.sound);
            this.play = function () {
                this.sound.play();
            }
            this.stop = function () {
                this.sound.pause();
            }
        }


        function everyinterval(n) {
            if ((myGameArea.frameNo / n) % 1 === 0) {
                return true;
            }
            return false;
        }

        function accelerate(n) {
            if (!myGameArea.interval) {
                myGameArea.interval = setInterval(updateGameArea, 20);
            }

            myGamePiece.gravity = n;


        }
        function postHS(){
            if (name == "") {
                alert("You must enter a name");
                return;
            }
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                 alert(this.responseText);
             }
        };
        xhttp.open("POST", "postScore.php", true);
        var myFormData = new FormData();
        myFormData.append("name", name);
        myFormData.append("score", Math.round(highscore/ (67 * sfX)));
        xhttp.send(myFormData);
        }
    </script>

    <br>

    <p style="font-family:Arial, Helvetica, sans-serif;font-size:16px;font-style:normal;">
        Press <b>SPACE</b> to jump! Avoid the obstacles and don't go out of bounds!</p>

    <button class="button button1" onClick="window.location='StartGameResize.html';">Back</button>
    <button class="button button1" onclick="postHS();" >Submit</button>
    <br> <br> Name: <input type="text" id="name" autocomplete="off">
    <button class="button button2" onclick="setName()">Enter</button></p>
</div>
</body>
</html>
