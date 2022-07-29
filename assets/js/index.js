layui.define(["element", "layer"], function (exports) {

	var $ = layui.$,
		$body = $("body"),
		element = layui.element,
		layer = layui.layer;

	var screen_size = {
		pc: [991, -1],
		pad: [768, 990],
		mobile: [0, 767]
	}

	var getDevice = function () {
		var width = $(window).width();
		for (var i in screen_size) {
			var sizes = screen_size[i],
				min = sizes[0],
				max = sizes[1];
			if (max == -1) max = width;
			if (min <= width && max >= width) {
				return i;
			}
		}
		return null;
	}

	var isDevice = function (label) {
		return getDevice() == label;
	}

	var isMobile = function () {
		return isDevice("mobile");
	}

	var Tab = function (el) {
		this.el = el;
		this.urls = [];
	}

	Tab.prototype.content = function (src) {
		var iframe = document.createElement("iframe");
		iframe.setAttribute("frameborder", "0");
		iframe.setAttribute("src", src);
		iframe.setAttribute("data-id", this.urls.length);
		return iframe.outerHTML;
	};

	Tab.prototype.is = function (url) {
		return (this.urls.indexOf(url) !== -1)
	};

	Tab.prototype.add = function (title, url) {
		if (this.is(url)) return false;
		this.urls.push(url);
		element.tabAdd(this.el, {
			title: title,
			content: this.content(url),
			id: url
		});
		this.change(url);
	};

	Tab.prototype.change = function (url) {
		element.tabChange(this.el, url);
	};

	Tab.prototype.delete = function (url) {
		element.tabDelete(this.el, url);
	};

	Tab.prototype.onChange = function (callback) {
		element.on("tab(" + this.el + ")", callback);
	};

	Tab.prototype.onDelete = function (callback) {
		var self = this;
		element.on("tabDelete(" + this.el + ")", function (data) {
			var i = data.index;
			self.urls.splice(i, 1);
			callback && callback(data);
		});
	};

	var Home = function () {

		var tabs = new Tab("tabs"),
			navItems = [];

		$("#Nav a").on("click", function (event) {
			// console.log(event);
			event.preventDefault();
			var $this = $(this),
				url = $this.attr("href"),
				title = $.trim($this.find("span").text());
			if (url && url !== "javascript:;") {
				if (tabs.is(url)) {
					tabs.change(url);
				} else {
					navItems.push($this);
					tabs.add(title, url);
				}
			}
		});

		// 默认触发第一个子菜单的点击事件layui-this

		//$("#Nav li.layui-nav-item:eq(0) > dl.layui-nav-child > dd > a:eq(0)").trigger("click");
		// $("#Nav li.layui-nav-item:eq(0) > dl.layui-nav-child > dd > a:eq(1)").trigger("click");
		// $("#Nav li.layui-nav-item:eq(0) > dl.layui-nav-child > dd > a:eq(2)").trigger("click");
		// $("#Nav li.layui-nav-item:eq(0) > dl.layui-nav-child > dd > a:eq(0)").trigger("click");
		tabs.onDelete(function (data) {
			var i = data.index;
			navItems.splice(i, 1);
		});

		this.slideSideBar();
	}

	Home.prototype.slideSideBar = function () {
		var $slideSidebar = $(".slide-sidebar"),
			$pageContainer = $(".layui-body"),
			$mobileMask = $(".mobile-mask");

		var isFold = false;
		$slideSidebar.click(function (e) {
			e.preventDefault();
			var $this = $(this),
				$icon = $this.find("i"),
				$admin = $body.find(".layui-layout-admin");
			var toggleClass = isMobile() ? "fold-side-bar-xs" : "fold-side-bar";
			if ($icon.hasClass("ai-menufold")) {
				$icon.removeClass("ai-menufold").addClass("ai-menuunfold");
				$admin.addClass(toggleClass);
				$("#logos").css({
					display: "inline-block",
					fontSize: "15px",
					marginRight: "25px",
					color: "#33cabb"

				});
				$("#logowz").css({
					display: "none",
				})
				isFold = true;
				if (isMobile()) $mobileMask.show();
			} else {
				$icon.removeClass("ai-menuunfold").addClass("ai-menufold");
				$admin.removeClass(toggleClass);
				$("#logos").css({
					fontSize: "25px",

				});
				$("#logowz").css({
					display: "inline-block",

				})
				isFold = false;
				if (isMobile()) $mobileMask.hide();
			}
		});

		var tipIndex;
		// 菜单收起后的模块信息小提示
		$("#Nav li > a").hover(function () {
			var $this = $(this);
			if (isFold) {
				tipIndex = layer.tips($this.find("em").text(), $this);
			}
		}, function () {
			if (isFold && tipIndex) {
				layer.close(tipIndex);
				tipIndex = null
			}
		})

		if (isMobile()) {
			$mobileMask.click(function () {
				$slideSidebar.trigger("click");
			});
		}
	}

	exports("home", new Home);
	$("#quit").click(function () {
		layer.confirm("确定退出当前登录账号吗？", {
			btn: ["确定", "取消"],
			icon: 3
		}, function () {
			layer.msg("正在退出账号中", {
				icon: 16,
				time: 1000
			}, function () {
				// setCookie("username", "", -1);
				// setCookie("password", "", -1)
				// setCookie("token", "", -1);
				setCookie("tab","primary.php");//记录tab的index primary.php
				$.get("./login.php?logout", function (e) {
					layer.msg("注销登录成功", {
						icon: 1
					});
				});
				// window.location.href="login.php?logout";
				setTimeout("window.location.reload()", 1000)

			});
		});

	});
	$("#update_password").click(function (event) {
		event.preventDefault();
		layer.open({
			type: 2,
			title: "修改密码",
			area: ["500px", "300px"],
			maxmin: false,
			content: "update_password.php"
		});
	});

	//一些事件触发
	element.on('tab(tabs)', function (data) {
		$(this).find(".mar").eq(0).one("click", function (e) {
			e.preventDefault();
			$(this).addClass("layui-anim-rotate layui-anim-loop");
			var id = $(this).parent().attr("lay-id");
			console.log($("iframe[src='" + id + "']").attr("src", $("iframe[src='" + id + "']").attr("src")))
			var _self = this;
			var iframe = $("iframe[src='" + id + "']").get(0);
			console.log(iframe)
			iframe.onload = iframe.onreadystatechange = function () {
				if (this.readyState && this.readyState != 'complete') return;
				else {
					$(_self).removeClass("layui-anim-rotate layui-anim-loop");
				}
			}
		});
	});


	element.on('nav(tabnav)',function(e){
		if(e.attr("href")!=undefined){
			setCookie("tab",e.attr("href"));//记录tab的index primary.php
		}
	});


	function setCookie(name, value) {
		var Days = 1;
		var exp = new Date();
		exp.setTime(exp.getTime() + Days * 24 * 60 * 60 * 30);
		document.cookie = name + "=" + encodeURIComponent(value) + ";expires=" + exp.toGMTString() + ";path=/";
	}

	function getCookie(cname) {
		var name = cname + "=";
		var ca = document.cookie.split(';');
		for (var i = 0; i < ca.length; i++) {
			var c = ca[i].trim();
			if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
		}
		return "";
	}

	function recordTab(){
		 var tabindex=getCookie("tab");
		 console.log(tabindex)
		if(tabindex == ""){
			$("a[href='primary.php']").trigger("click");
			$("a[href='primary.php']").parent().attr("class", "layui-this");
		}else{
			$("a[href='"+tabindex+"']").trigger("click");
			$("a[href='"+tabindex+"']").parent().attr("class", "layui-this");
		}
	}

	function int() {
		$.ajax({
			url: "php/admin.php",
			type: "POST",
			dataType: "json",
			success: function (data) {
				console.log(data);
				$("#username").text(data.data.username);
			},
			error: function (data) {
				console.log(data);
				layer.msg(data.responseText, {
					icon: 5
				});
			}
		});
	}
	//int();
	recordTab();
});
