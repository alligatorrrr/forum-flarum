<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
	<title>Website Under Maintenance</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style>
		body {
			font-family: "Open Sans", sans-serif;
			background-color: #fafafa;
			color: #444444;
			text-align: center;
			padding-top: 50px;
		}
		h1 {
			font-size: 36px;
			font-weight: 700;
			margin-bottom: 20px;
			color: #444444;
		}
		p {
			font-size: 18px;
			margin-bottom: 30px;
			line-height: 1.5;
		}
		.countdown-wrapper {
			display: flex;
			justify-content: center;
			align-items: center;
			margin-bottom: 30px;
		}
		.countdown-item {
			display: flex;
			flex-direction: column;
			align-items: center;
			margin: 0 10px;
			background-color: #ffffff;
			border-radius: 8px;
			box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
			padding: 20px;
		}
		.countdown-item > span {
			font-size: 30px;
			font-weight: 700;
			margin-bottom: 10px;
			color: #444444;
		}
		.countdown-item > small {
			font-size: 14px;
			text-transform: uppercase;
			color: #777777;
		}
		@media (min-width: 768px) {
			h1 {
				font-size: 48px;
			}
			p {
				font-size: 20px;
			}
			.countdown-item > span {
				font-size: 36px;
			}
			.countdown-item > small {
				font-size: 16px;
			}
		}
	</style>
	<script>
		// Set the date and time of when the website will reopen
		var countDownDate = new Date("2024-01-21T23:00:00Z").getTime();

		// Update the countdown timer every second
		var x = setInterval(function() {

			// Get the current date and time
			var now = new Date().getTime();

			// Calculate the time remaining between now and the countdown date
			var distance = countDownDate - now;

			// Calculate the days, hours, minutes, and seconds remaining
			var days = Math.floor(distance / (1000 * 60 * 60 * 24));
			var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
			var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
			var seconds = Math.floor((distance % (1000 * 60)) / 1000);

			// Display the countdown timer on the page
			document.getElementById("days").innerHTML = days;
			document.getElementById("hours").innerHTML = hours;
			document.getElementById("minutes").innerHTML = minutes;
			document.getElementById("seconds").innerHTML = seconds;

			// If the countdown is over, display a message saying the website is open
			if (distance < 0) {
				clearInterval(x);
				document.getElementById("countdown").innerHTML = "Website is open!";
			
			document.getElementById("countdown").style.display = "block";
		}
	}, 1000);
</script>
</head>
<body>
	<h1>小白狗维护中</h1>
	<p>论坛更新维护期间，可以加q群：362960496 吹水聊天。更新情况如有变动，会在微博：<a href="https://m.weibo.cn/u/7643769852?jumpfrom=weibocom">村口大喇叭开始广播</a>发布</p>
	<div class="countdown-wrapper">
		<div class="countdown-item">
			<span id="days">0</span>
			<small>Days</small>
		</div>
		<div class="countdown-item">
			<span id="hours">0</span>
			<small>Hours</small>
		</div>
		<div class="countdown-item">
			<span id="minutes">0</span>
			<small>Minutes</small>
		</div>
		<div class="countdown-item">
			<span id="seconds">0</span>
			<small>Seconds</small>
		</div>
	</div>
	<p id="countdown" style="display: none;">Website is open!</p>
</body>
</html>