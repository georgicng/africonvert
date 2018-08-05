$(document).ready(function () {
  window.search_collapse = function () {
    $("#s_btn").click(function () {

      $(".input_s_div").css({
        display: "block"
      })

      $(".s_btn_div").css({
        display: "none"
      })

    });

    $(".s_btn2").click(function () {

      $(".input_s_div").css({
        display: "block"
      })

      $(".s_btn_div").css({
        display: "none"
      })

    });


    $("#s_btn_close").click(function () {

      $(".input_s_div").css({
        display: "none"
      })

      $(".s_btn_div").css({
        display: "block"
      })

    });

    // Search function ends
  }
  window.home_tab_handler = function () {
    $("#tab_off1").click(function () {

      $("#tab_on1").css({
        display: "block"
      })

      $("#tab_on2").css({
        display: "none"
      })
      $("#tab_on3").css({
        display: "none"
      })
      $("#tab_on4").css({
        display: "none"
      })

      $("#tab_off2").css({
        display: "block"
      })
      $("#tab_off1").css({
        display: "none"
      })
      $("#tab_off3").css({
        display: "block"
      })
      $("#tab_off4").css({
        display: "block"
      })

      $("#column_div").css({
        display: "block"
      })
      $("#column_div_2").css({
        display: "none"
      })
      $("#column_div_3").css({
        display: "none"
      })
      $("#column_div_4").css({
        display: "none"
      })

    });


    $("#tab_off2").click(function () {

      $("#tab_on2").css({
        display: "block"
      })

      $("#tab_on1").css({
        display: "none"
      })
      $("#tab_on3").css({
        display: "none"
      })
      $("#tab_on4").css({
        display: "none"
      })

      $("#tab_off1").css({
        display: "block"
      })
      $("#tab_off2").css({
        display: "none"
      })
      $("#tab_off3").css({
        display: "block"
      })
      $("#tab_off4").css({
        display: "block"
      })

      $("#column_div").css({
        display: "none"
      })
      $("#column_div_2").css({
        display: "block"
      })
      $("#column_div_3").css({
        display: "none"
      })
      $("#column_div_4").css({
        display: "none"
      })

    });

    $("#tab_off3").click(function () {

      $("#tab_on3").css({
        display: "block"
      })

      $("#tab_on1").css({
        display: "none"
      })
      $("#tab_on2").css({
        display: "none"
      })
      $("#tab_on4").css({
        display: "none"
      })

      $("#tab_off1").css({
        display: "block"
      })
      $("#tab_off2").css({
        display: "block"
      })
      $("#tab_off3").css({
        display: "none"
      })
      $("#tab_off4").css({
        display: "block"
      })

      $("#column_div").css({
        display: "none"
      })
      $("#column_div_2").css({
        display: "none"
      })
      $("#column_div_3").css({
        display: "block"
      })
      $("#column_div_4").css({
        display: "none"
      })


    });

    $("#tab_off4").click(function () {

      $("#tab_on4").css({
        display: "block"
      })

      $("#tab_on1").css({
        display: "none"
      })
      $("#tab_on2").css({
        display: "none"
      })
      $("#tab_on3").css({
        display: "none"
      })

      $("#tab_off1").css({
        display: "block"
      })
      $("#tab_off2").css({
        display: "block"
      })
      $("#tab_off3").css({
        display: "block"
      })
      $("#tab_off4").css({
        display: "none"
      })

      $("#column_div").css({
        display: "none"
      })
      $("#column_div_2").css({
        display: "none"
      })
      $("#column_div_3").css({
        display: "none"
      })
      $("#column_div_4").css({
        display: "block"
      })


    }); // Left Menu Tabs function ends

  }

  window.home_side_tab_handler = function () {

    $("#b_taboff1").click(function () {

      $("#b_tabon1").css({
        display: "block"
      })

      $("#b_tabon2").css({
        display: "none"
      })
      $("#b_tabon3").css({
        display: "none"
      })

      $("#b_taboff1").css({
        display: "none"
      })
      $("#b_taboff2").css({
        display: "block"
      })
      $("#b_taboff3").css({
        display: "block"
      })

      $("#blog_main_div").css({
        display: "none"
      })
      $("#blog_main_div_1").css({
        display: "block"
      })
      $("#blog_main_div_2").css({
        display: "none"
      })
      $("#blog_main_div_3").css({
        display: "none"
      })

    });

    $("#b_taboff2").click(function () {

      $("#b_tabon2").css({
        display: "block"
      })

      $("#b_tabon1").css({
        display: "none"
      })
      $("#b_tabon3").css({
        display: "none"
      })

      $("#b_taboff1").css({
        display: "block"
      })
      $("#b_taboff2").css({
        display: "none"
      })
      $("#b_taboff3").css({
        display: "block"
      })

      $("#blog_main_div").css({
        display: "none"
      })
      $("#blog_main_div_1").css({
        display: "none"
      })
      $("#blog_main_div_2").css({
        display: "block"
      })
      $("#blog_main_div_3").css({
        display: "none"
      })

    });

    $("#b_taboff3").click(function () {

      $("#b_tabon3").css({
        display: "block"
      })

      $("#b_tabon1").css({
        display: "none"
      })
      $("#b_tabon2").css({
        display: "none"
      })

      $("#b_taboff1").css({
        display: "block"
      })
      $("#b_taboff2").css({
        display: "block"
      })
      $("#b_taboff3").css({
        display: "none"
      })

      $("#blog_main_div").css({
        display: "none"
      })
      $("#blog_main_div_1").css({
        display: "none"
      })
      $("#blog_main_div_2").css({
        display: "none"
      })
      $("#blog_main_div_3").css({
        display: "block"
      })

    });
  }
  // blog Menu Tabs function ends

  $(document).on('click', '.panel-heading span.clickable', function (e) {
    var $this = $(this);
    if (!$this.hasClass('panel-collapsed')) {
      $this.parents('.panel').find('.panel-body').slideUp();
      $this.addClass('panel-collapsed');
      $this.find('i').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
    } else {
      $this.parents('.panel').find('.panel-body').slideDown();
      $this.removeClass('panel-collapsed');
      $this.find('i').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
    }
  })
}); // End