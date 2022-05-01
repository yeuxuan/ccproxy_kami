<?php
include '../includes/common.php';
if (!($islogin == 1)) {
    exit('<script language=\'javascript\'>alert("您还没有登录，请先登录！");window.location.href=\'login.php\';</script>');
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>
			<?php echo $subconf['hostname']."后台管理首页" ?>
		</title>
		<?php
	    include("foot.php");
    	?>

		<meta name="viewport" content="width=device-width, initial-1.0"/>
		<link rel="stylesheet" type="text/css" href="./css/theme.css" />
		<link rel="stylesheet" type="text/css" href="./css/home.css" />
	</head>
	<body>
		<div class="layui-row layui-col-space12" style="margin-bottom: 10px;">
			<div class="layui-col-xs12 layui-col-sm6 layui-col-md3">
				<div class="card" style="background-color: #33cabb;">
					<div class="card-icon">
						<div class="card-icon-bor">
							<i class="layui-icon layui-icon-flag"></i>
						</div>
					</div>
					<div class="card-box">
						<div class="card-box-title">正在加载</div>
						<div class="card-box-num">0.00</div>
					</div>
				</div>
			</div>
			<div class="layui-col-xs12 layui-col-sm6 layui-col-md3">
				<div class="card" style="background-color: #ce68fd;">
					<div class="card-icon">
						<div class="card-icon-bor">
							<i class="layui-icon layui-icon-rmb"></i>
						</div>
					</div>
					<div class="card-box">
						<div class="card-box-title">正在加载</div>
						<div class="card-box-num">0.00</div>
					</div>
				</div>
			</div>
			<div class="layui-col-xs12 layui-col-sm6 layui-col-md3">
				<div class="card" style="background-color: #f96868;">
					<div class="card-icon">
						<div class="card-icon-bor">
							<i class="layui-icon layui-icon-component"></i>
						</div>
					</div>
					<div class="card-box">
						<div class="card-box-title">正在加载</div>
						<div class="card-box-num">0.00</div>
					</div>
				</div>
			</div>
			<div class="layui-col-xs12 layui-col-sm6 layui-col-md3">
				<div class="card" style="background-color: #faa123;">
					<div class="card-icon">
						<div class="card-icon-bor">
							<i class="layui-icon layui-icon-find-fill"></i>
						</div>
					</div>
					<div class="card-box">
						<div class="card-box-title">正在加载</div>
						<div class="card-box-num">0.00</div>
					</div>
				</div>
			</div>
			<div class="layui-col-xs12 layui-col-sm6 layui-col-md3">
				<div class="card" style="background-color: #fc4086;">
					<div class="card-icon">
						<div class="card-icon-bor">
							<i class="layui-icon layui-icon-template-1"></i>
						</div>
					</div>
					<div class="card-box">
						<div class="card-box-title">正在加载</div>
						<div class="card-box-num">0.00</div>
					</div>
				</div>
			</div>
			<div class="layui-col-xs12 layui-col-sm6 layui-col-md3">
				<div class="card" style="background-color: #4d7fff;">
					<div class="card-icon">
						<div class="card-icon-bor">
							<i class="layui-icon layui-icon-snowflake"></i>
						</div>
					</div>
					<div class="card-box">
						<div class="card-box-title">正在加载</div>
						<div class="card-box-num">0.00</div>
					</div>
				</div>
			</div>
			<div class="layui-col-xs12 layui-col-sm6 layui-col-md3">
				<div class="card" style="background-color: #009688;">
					<div class="card-icon">
						<div class="card-icon-bor">
							<i class="layui-icon layui-icon-tree"></i>
						</div>
					</div>
					<div class="card-box">
						<div class="card-box-title">正在加载</div>
						<div class="card-box-num">0.00</div>
					</div>
				</div>
			</div>
			<div class="layui-col-xs12 layui-col-sm6 layui-col-md3">
				<div class="card" style="background-color: #5fb878;">
					<div class="card-icon">
						<div class="card-icon-bor">
							<i class="layui-icon layui-icon-website"></i>
						</div>
					</div>
					<div class="card-box">
						<div class="card-box-title">正在加载</div>
						<div class="card-box-num">0.00</div>
					</div>
				</div>
			</div>
		</div>
		<div class="layui-row layui-col-space16">
			<div class="layui-col-xs12 layui-col-sm12 layui-col-md6">
				<div class="layui-card">
					<div class="layui-card-header layui-form">
						<!-- <span>趋势图</span>
						<div class="tool">
							<input type="radio" name="dateRange" value="7" title="近7天" lay-filter="dateRange" checked>
							<input type="radio" name="dateRange" value="15" title="近15天" lay-filter="dateRange">
							<input type="radio" name="dateRange" value="30" title="近30天" lay-filter="dateRange">
						</div> -->
					</div>
					<div class="layui-card-body" style="height: 55vh;">
						<!-- <div id="container" style="height: 100%;width: 100%;">
							<div class="lod">
								<i class="layui-icon layui-icon-loading layui-icon layui-anim layui-anim-rotate layui-anim-loop"></i>
							</div>
						</div> -->
					</div>
				</div>
			</div>
			<div class="layui-col-xs12 layui-col-sm12 layui-col-md6">
				<div class="layui-card">
					<div class="layui-card-header">
						<span class="title">站点信息</span>
						<div class="tool">
							<!-- <span style="margin-right: 5px;">选择日期</span>
							<div class="layui-input-inline" style="width: 150px;">
								<input type="text" class="layui-input" id="date" placeholder="YYYY-MM-DD">
							</div>
							<button type="button" class="layui-btn layui-btn-sm layui-btn-primary" id="renovate">刷新</button> -->
						</div>
					</div>
					<div class="layui-card-body" style="height: auto;">
						<!-- <table id="data" lay-filter="data"></table> -->
						<div class="layui-row layui-col-space20">
							<div class="layui-col-xs12 layui-col-sm16 layui-col-md12">
							<blockquote class="layui-elem-quote">
  									<div id="test2">网站到期时间：
										  <?php
											$expir=strtotime($subconf['over_date'])-strtotime($date);
											$diff_days = floor($expir/86400);
											if($diff_days<=7){
												echo "<span style='color:#fce38a;font-weight: bold;'>还剩 ".$diff_days.' 天'."</span>";
											}else{
												echo "<span style='color:#fce38a;font-weight: bold;'> ".$subconf['over_date'].''."</span>";
											}
										   ?>
										   </div>
							</blockquote>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
	<!-- <script type="text/javascript" src="https://www.layuicdn.com/layui/layui.js"></script> -->
	<!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script> -->
	<!-- <script src="../js/walden.js"></script> -->
	<script type="text/html" id="ratioTpl">
		<div class="layui-progress" lay-showPercent="yes"><div class="layui-progress-bar {{ d.ratio <= d.refund_quota ? "" : "layui-bg-red"}}" lay-percent="{{d.ratio}}%"></div></div>
	</script>
	<script>
		layui.use(["jquery", "table", "laydate", "form", "upload", "element"], function() {
			var $ = layui.$,
				table = layui.table,
				laydate = layui.laydate,
				form = layui.form,
				upload = layui.upload,
				element = layui.element,
				date = 7,
				state = true;
			form.on("radio(dateRange)", function(data) {
				date = data.value;
				layer.msg("加载中", {
					icon: 16,
					shade: 0.01,
					time: false
				});
				int(date);
			});
			table.render({
				elem: "#data",
				url: "../php/data.php",
				title: "模拟数据",
				//height: 500,
				skin: "nob",
				initSort: {
					field: "value",
					type: "desc"
				},
				loading: false,
				where: {
					date: $("#date").val()
				},
				cols: [
					[{
							title: "排名",
							type: "numbers"
						}, {
							field: "username",
							title: "数据A",
							width: 100,
							align: "center",
							unresize: true
						}, {
							field: "goodsNum",
							title: "数据B",
							width: 80,
							align: "center",
							unresize: true
						}, {
							field: "payment",
							title: "数据C",
							width: 100,
							align: "center",
							unresize: true,
							style: "color:red"
						}, {
							field: "value",
							title: "数据D",
							width: 140,
							align: "center",
							unresize: true,
							style: "color:red",
							hide: true
						},
						{
							field: "refund",
							title: "数据F",
							width: 100,
							align: "center",
							unresize: true,
							style: "color:#009688"
						},
						{
							field: "total",
							title: "数据G",
							width: 100,
							align: "center",
							unresize: true,
							style: "color:red"
						},
						{
							title: "数据H",
							templet: "#ratioTpl",
							align: "center",
							unresize: true
						}
					]
				],
				done: function(res, curr, count) {
					$(".title").text(res.msg);
					$("#date").val(res.date);
					element.init();
				}
			});
			laydate.render({
				elem: "#date",
				done: function() {
					reload("data");
				}
			});
			$("#renovate").click(function() {
				reload("data");
			});

			function int(date) {
				// $.ajax({
				// 	url: "../php/home.php",
				// 	type: "POST",
				// 	dataType: "json",
				// 	data: {
				// 		date: date
				// 	},
				// 	success: function(data) {
				// 		if (data.icon == "1") {
				// 			init(data.data.echarts);
				// 			card(data.data.card);
				// 			layer.closeAll();
				// 		} else {
				// 			parent.layer.msg("您的账户在其他设备登录", function() {
				// 				parent.location.reload();
				// 			});
				// 		}
				// 	}
				// });
				init();
				layer.closeAll();
			}

			function card(data) {
				var i = 0;
				for (var key in data) {
					var elem = $(".card").eq(i);
					var json = data[key];
					var name = json.name;
					var node = elem.find(".card-box-num");
					elem.find(".card-box-title").text(name);
					i++;
					num(node, json);
				}
				state = false;
			}

			function num(elem, json) {
				if (state == true) {
					if (json.value > 10) {
						var c = (json.value / 100).toFixed(0);
						for (var i = 0; i <= 100; i++) {
							(function(i) {
								setTimeout(function() {
									elem.text(i * c);
									if (i == 100) {
										elem.text(json.num);
									}
								}, (i + 1) * 10);
							})(i);
						}
					} else {
						elem.text(json.num);
					}
				} else {
					elem.text(json.num);
				}
			}

			// function init(data) {
			// 	var dom = document.getElementById("container");
			// 	var myChart = echarts.init(dom, "walden");
			// 	var app = {};
			// 	option = null;
			// 	option = {
			// 		xAxis: {
			// 			type: "category",
			// 			data: ["2020-10-10", "2020-10-11"]
			// 		},
			// 		yAxis: {
			// 			type: "value"
			// 		},
			// 		tooltip: {
			// 			trigger: "axis",
			// 			axisPointer: {
			// 				type: "cross",
			// 				label: {
			// 					show: false,
			// 					backgroundColor: "rgba(0,0,0,0.5)"
			// 				}
			// 			}
			// 		},
			// 		legend: {
			// 			show: true,
			// 			data: ["模拟数据"]
			// 		},
			// 		series: [{
			// 			data: [100, 500, 600],
			// 			name: "模拟数据",
			// 			type: "line",
			// 			smooth: true,
			// 			label: {
			// 				normal: {
			// 					show: false,
			// 					position: "top"
			// 				}
			// 			},
			// 			itemStyle: {
			// 				normal: {
			// 					color: "#33cabb",
			// 					lineStyle: {
			// 						color: "#33cabb"
			// 					}
			// 				}
			// 			}
			// 		}]
			// 	};
			// 	if (option && typeof option === "object") {
			// 		myChart.setOption(option, true);
			// 	}
			// }

			function reload(id) {
				layui.use(["jquery", "table"], function() {
					var $ = layui.$,
						table = layui.table;
					table.reload(id, {
						where: {
							date: $("#date").val()
						}
					});
				});
			}
			// int(date);
			// setInterval(function() {
			// 	int(date);
			// }, 60000);
		});
	</script>
</html>
