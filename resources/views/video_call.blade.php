<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Video Call</title>

	<script type="text/javascript">
		var access_token = "<?php echo $access_token ?>";
	</script>

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.4.0.min.js"></script>
	<script src="{{ asset('js/socket.io-2.2.0.js') }}"></script>
	<script src="{{ asset('js/StringeeSDK-1.5.10.js') }}"></script>
	<script src="{{ asset('js/video_call.js') }}"></script>

	<style>
        .video-container {
            border: none;
            width: 100%;
            max-width: 960px;
            min-height: 600px;
            height: 100vh;
            margin: 0 auto;
        }
        .video-container .video-left {
            width: 33%;
            min-width: 280px;
            border-right: 1px groove #9E9E9E;
            background: #9E9E9E;
        }
        .video-container .video-right {
            background: #795548;
        }

        #incoming-call-div {
            display: none;
        }

        #remoteVideo {
            width: 100%;
            height: 700px;
            background: #424141;
            position: absolute;
        }

        #localVideo {
            width: 20%;
            background: #888888;
            position: fixed;
            right: 20px;
            top: 20px;
        }
        
        .wrapper {
            position: relative;
        }

        .button_group {
            position: absolute;
            z-index: 999;
            left: 40%;
            top: 600px;
        }

        .username, .modal-title {
            color: white;
        }

        .modal-body img {
            width: 100%;
        }

        .modal-content {
            background-color: #2973d9;
        }

        button {
            border-radius: 50px !important;
            height: 50px;
            width: 50px;
        }

        .conversion {
            position: absolute;
        }

        a {
            text-decoration: none;
            color: #232323;
            transition: color 0.3s ease;
        }

        a:hover {
            color: green;
            text-decoration: none;
        }

        #menuToggle {
            display: block;
            position: fixed;
            top: 20px;
            left: 50px;
            z-index: 1;
            -webkit-user-select: none;
            user-select: none;
        }

        #menuToggle input {
            display: block;
            width: 40px;
            height: 32px;
            position: absolute;
            top: -7px;
            left: -5px;
            cursor: pointer;
            opacity: 0; /* hide this */
            z-index: 2; /* and place it over the hamburger */
            -webkit-touch-callout: none;
        }

        /*
         * Just a quick hamburger
         */
        #menuToggle span {
            display: block;
            width: 33px;
            height: 4px;
            margin-bottom: 5px;
            position: relative;

            background: #cdcdcd;
            border-radius: 3px;

            z-index: 1;

            transform-origin: 4px 0px;

            transition: transform 0.5s cubic-bezier(0.77,0.2,0.05,1.0),
                      background 0.5s cubic-bezier(0.77,0.2,0.05,1.0),
                      opacity 0.55s ease;
        }

        #menuToggle span:first-child {
            transform-origin: 0% 0%;
        }

        #menuToggle span:nth-last-child(2) {
            transform-origin: 0% 100%;
        }

        /* 
         * Transform all the slices of hamburger
         * into a crossmark.
         */
        #menuToggle input:checked ~ span {
            opacity: 1;
            transform: rotate(45deg) translate(-2px, -1px);
            background: #232323;
        }

        /*
         * But let's hide the middle one.
         */
        #menuToggle input:checked ~ span:nth-last-child(3) {
            opacity: 0;
            transform: rotate(0deg) scale(0.2, 0.2);
        }

        /*
         * Ohyeah and the last one should go the other direction
         */
        #menuToggle input:checked ~ span:nth-last-child(2) {
            transform: rotate(-45deg) translate(0, -1px);
        }

        /*
         * Make this absolute positioned
         * at the top left of the screen
         */
        #menu {
            width: 300px;
            margin: -50px 0 0 -50px;
            padding: 20px;
            padding-top: 45px;
            background: rgba(237,237,237, 0.5);
            list-style-type: none;
            -webkit-font-smoothing: antialiased;
            transform-origin: 0% 0%;
            transform: translate(-100%, 0);
            transition: transform 0.5s cubic-bezier(0.77,0.2,0.05,1.0);
            height: 500px;
            overflow: hidden;
            overflow-y: scroll;
        }

        #menu li {
            padding: 5px 0;
            font-size: 18px;
            font-weight: initial !important;
        }

        /*
         * And let's slide it in from the left
         */
        #menuToggle input:checked ~ ul {
            transform: none;
        }

    </style>
</head>
<body>
	<div class="wrapper">

		<div class="button_group text-center">
			<input value="" class="form-control" id="callTo" type="hidden" name="toUsername">

			<p class="text-center username"></p>

			<button id="videoCallBtn" onclick="testMakeCall(true)" class="btn btn-success" data-toggle="tooltip" title="Call">
				<i class="glyphicon glyphicon-facetime-video"></i>
			</button>
			&nbsp;&nbsp;&nbsp;
			<button id="muteBtn" onclick="mute()" class="btn btn-default" data-toggle="tooltip" title="Mute / Unmute">
				<i class="glyphicon glyphicon-volume-up"></i>
			</button>
			&nbsp;&nbsp;&nbsp;
			<button id="enableVideoBtn" onclick="enableVideo()" class="btn btn-warning" data-toggle="tooltip" title="Enable / Disable Video">
				<i class="glyphicon glyphicon-remove"></i>
			</button>
			&nbsp;&nbsp;&nbsp;
			<button class="btn btn-danger" data-toggle="tooltip" title="End" onclick="end_call()">
				<i class="glyphicon glyphicon-earphone"></i>
			</button>

		</div>

		<div id="incoming-call-div">
			<br />
			<button id="answerBtn" onclick="testAnswerCall()" class="btn btn-xs btn-success">Trả Lời</button>
			<button id="rejectBtn" onclick="testRejectCall()" class="btn btn-xs btn-danger">Từ Chối</button>
		</div>

		<div style="position: relative;">
			<video id="remoteVideo" autoplay></video>
			<video id="localVideo" autoplay muted></video>
		</div>
	</div>

	<div class="modal fade" id="myModal" role="dialog">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title text-center">Ringing</h4>
				</div>
				<div class="modal-body">
					<img src="{{ asset('img/ringing.gif') }}" alt="ringing">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
</body>
</html>