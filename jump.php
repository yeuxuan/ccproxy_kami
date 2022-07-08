
<!DOCTYPE html>
<html lang="zh">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $title; ?></title>
	<link rel="stylesheet" type="text/css" href="./assets/css/bootstrap.min.css" />
	<link rel="stylesheet" type="text/css" href="./assets/css/htmleaf-demo.css">
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport">
<meta content="yes" name="apple-mobile-web-app-capable">
<meta content="black" name="apple-mobile-web-app-status-bar-style">
<meta name="format-detection" content="telephone=no">
<meta content="false" name="twcClient" id="twcClient">
	<style type="text/css">
		.loader {
		    width: 320px;
		    margin: 50px auto 70px;
		    position: relative;
		}
		.loader .loading-1 {
        	margin:0px auto;
		    position: relative;
		    width: 70%;
		    height: 10px;
		    border: 1px solid #69d2e7;
		    border-radius: 10px;
		    animation: turn 4s linear 1.75s infinite;
		}
		.loader .loading-1:before {
		    content: "";
		    display: block;
		    position: absolute;
		    width: 0%;
		    height: 100%;
		    background: #69d2e7;
		    box-shadow: 10px 0px 15px 0px #69d2e7;
		    animation: load 2s linear infinite;
		}
		.loader .loading-2 {
		    width: 100%;
		    position: absolute;
		    top: 20px;
		    color: #69d2e7;
		    font-size: 22px;
		    text-align: center;
		    animation: bounce 2s  linear infinite;
		}
		@keyframes load {
		    0% {
		        width: 0%;
		    }
		    87.5%, 100% {
		        width: 100%;
		    }
		}
		@keyframes turn {
		    0% {
		        transform: rotateY(0deg);
		    }
		    6.25%, 50% {
		        transform: rotateY(180deg);
		    }
		    56.25%, 100% {
		        transform: rotateY(360deg);
		    }
		}
		@keyframes bounce {
		    0%,100% {
		        top: 10px;
		    }
		    12.5% {
		        top: 30px;
		    }
		}
        footer img{
        width:50px;
        
        }
        footer{
        	text-align:center;
            margin-bottom:40px;
            margin-top:60%;
        }
        h5{
        	text-align:center;
        }
	</style>
<meta http-equiv="refresh" content="0.1;url=mttbrowser://url=<?php echo $t_url; ?>">

</head>
<body>
<?php echo $ding; ?>
	<div class="htmleaf-container">
	<header class="htmleaf-header">
			<h1>小姐姐正在请求你<span>使用其他浏览器打开本站</span></h1>
		</header>
		<div class="demo" >
		        <div class="container">
		            <div class="row">
		                <div class="col-md-12">
		                    <div class="loader">
		                        <div class="loading-1"></div>
		                        <div class="loading-2">因QQ内置浏览器协议问题<br>请点击右上角使用其他浏览器</div>
		                    </div>
		                </div>
		            </div>
		        </div>
		    </div>
	</div>
    <footer>
    <h5>点击下方已安装的图标直接跳转</h5>
    <a href="mttbrowser://url=<?php echo $t_url; ?>"><img src="./assets/img/mtt.png"></img></a>
    <a href="googlechrome://browse?url=<?php echo $t_url; ?>"><img src="./assets/img/360chrome.png"></img></a>
    <a href="googlechrome://browse?url=<?php echo $t_url; ?>"><img src="./assets/img/chrome.png"></img></a>
     <a href="ucweb://<?php echo $t_url; ?>"><img src="./assets/img/UCMobile.png"></img></a>
    <a href="mibrowser:<?php echo $t_url; ?>"><img src="./assets/img/Mbrowser.png"></img></a>
    </footer>


<script>
            window.onload = function() {
                document.body.addEventListener("touchmove", function(evt) {
					console.log(evt._isScroller)
                    if (!evt._isScroller) {
                        evt.preventDefault();
                    }
                });
                var gamerule = document.querySelector(".gamerule");
                var overscroll = function(el) {
                    el.addEventListener("touchstart", function() {
                        var top = el.scrollTop,
                            totalScroll = el.scrollHeight,
                            currentScroll = top + el.offsetHeight;
                        if (top === 0) {
                            el.scrollTop = 1;
                        } else if (currentScroll === totalScroll) {
                            el.scrollTop = top - 1;
                        }
                    });
                    el.addEventListener("touchmove", function(evt) {
                        if (el.offsetHeight < el.scrollHeight)  
                            evt._isScroller = true;
                    });
                };
            };
        </script>
</body>
</html>