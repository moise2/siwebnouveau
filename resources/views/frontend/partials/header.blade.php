{{-- <header id="top-header"> --}}
    @include('frontend.components.top-header')
{{-- </header> --}}

{{-- @include('frontend.components.main-menu') --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
let lastScrollTop = 0;
const topHeader = document.querySelector('.top-header');

window.addEventListener('scroll', function() {
    let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

    if (scrollTop > lastScrollTop) {
        // Scroll Down
        topHeader.classList.add('hide-header');
    } else {
        // Scroll Up
        topHeader.classList.remove('hide-header');
    }
    lastScrollTop = scrollTop <= 0 ? 0 : scrollTop; // Pour Mobile or negative scrolling
});



</script>

<script>
    $(window).on('scroll',function(){
        if(Math.round($(window).scrollTop())>0){
            $('#custom-navbar').addClass('custom-navbar-scrolling');
        }
        else{
            $('#custom-navbar').removeClass('custom-navbar-scrolling');
        }
    })
</script>
