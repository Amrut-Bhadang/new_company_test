$(document).ready(function() {
  var owl = $('.testi-carousel');
  owl.owlCarousel({
      margin: 10,
      rtl:true,
      ltr:true,
      nav:true,
      dots:false,
      loop: true,
      responsive: {
        0: {
          items: 1
        },
        577: {
          items: 1
        },
        992: {
          items: 1
        }
      }
  });
});
$(document).ready(function() {
  var owl = $('.detail_slider .owl-carousel');
  owl.owlCarousel({
      margin: 0,
      nav:true,
      rtl:true,
      ltr:true,
      dots:false,
      loop: true,
      responsive: {
        0: {
          items: 1
        },
        577: {
          items: 1
        },
        992: {
          items: 1
        }
      }
  });
});
$(document).ready(function() {
  var owl = $('.slider_type1.owl-carousel');
  owl.owlCarousel({
      margin: 20,
      nav:true,
      rtl:true,
      ltr:true,
      dots:false,
      loop: true,
      responsive: {
        0: {
          items: 1
        },
        470: {
          items: 2
        },
        992: {
          items: 3
        },
        1401: {
          items: 4
        }
      }
  });
});
 $(".navbar-toggler").click(function(){
    $("body").addClass("open_nav");
  });
  $(".btn_cross").click(function(){
    $("body").removeClass("open_nav");
  });
  $(".filter_pop").click(function(){
    $("body").addClass("open_filter");
  });
  $(".filter_cross").click(function(){
    $("body").removeClass("open_filter");
  });
  $(document).ready(function(){
      $('.court-box .court-location').matchHeight();
  })