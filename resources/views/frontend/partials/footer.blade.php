<footer id="page-specific-footer" class="footer py-5" style="background-color: #f7f7f7; color: #333;">
    <div class="container">
        <div class="row justify-content-between align-items-center text-center text-md-start">
            <!-- Togo Coat of Arms Section -->
            <div class="col-12 col-md-4 mb-4 mb-md-0 text-center text-md-start">
                <img src="{{ asset('assets/img/20210406125513_Armoiries_du_Togo__1_-removebg-preview.png') }}" alt="armoirie" width="90" height="90" class="img-fluid" style="opacity: 0.9; transition: transform 0.3s;">
            </div>

            <!-- Contact Section -->
            <div class="col-12 col-md-4 mb-4 mb-md-0">
                <a href="{{ url('/contact') }}" style="text-decoration: none; color: #d92323;">
                    <h4 class="contact-title" style="font-weight: bold; font-size: 1.5rem;">Contact</h4>
                </a>
                <ul class="list-unstyled mb-0">
                    <li><a href="mailto:contact@example.com" class="text-dark text-decoration-none contact-link">spreformetg@gmail.com</a></li>
                    <li><a href="tel:+22891210176" class="text-dark text-decoration-none contact-link">+228 91210176</a></li>
                </ul>
            </div>

            <!-- Social Media Icons -->
            <div class="col-12 col-md-4 text-center text-md-end">
                <div class="d-flex justify-content-center justify-content-md-end">
                    <a href="https://www.facebook.com" target="_blank" class="text-danger social-icon me-3">
                        <i class="fab fa-facebook-f fa-lg"></i>
                    </a>
                    <a href="https://twitter.com" target="_blank" class="text-danger social-icon me-3">
                        <i class="fab fa-twitter fa-lg"></i>
                    </a>
                    <a href="https://www.linkedin.com" target="_blank" class="text-danger social-icon">
                        <i class="fab fa-linkedin-in fa-lg"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Section -->
    <div class="footer-bottom text-center py-3" style="background-color: #cecaca; color: #474747;">
        <p class="mb-0">©Perfodev Sarl tous droits réservés,2024 <span 2024></span></p>
    </div>
</footer>

<style>
    /* Animation on hover */
    #page-specific-footer img:hover {
        transform: scale(1.1);
    }

    .contact-link:hover {
        color: #d92323;
        transition: color 0.3s ease-in-out;
    }

    .social-icon {
        transition: color 0.3s ease, transform 0.3s ease;
    }

    .social-icon:hover {
        color: #333;
        transform: scale(1.2);
    }

    /* Contact Title Style */


    /* Responsive Padding Adjustments */
    @media (max-width: 767px) {
        .footer-bottom {
            padding-top: 15px;
            padding-bottom: 15px;
        }
    }
</style>

<script>
    // Script to dynamically update the year
    document.getElementById('year').textContent = new Date().getFullYear();
</script>




<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>


<script src="https://cdn.amcharts.com/lib/5/index.js"></script>
<script src="https://cdn.amcharts.com/lib/5/map.js"></script>
<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
<script src="https://cdn.amcharts.com/lib/5/geodata/data/countries2.js"></script>
<script src="https://cdn.amcharts.com/lib/5/geodata/worldLow.js"></script>
   <!-- AMCHARS 4 -->
<script src="assets/amcharts4/core.js"></script>
<script src="assets/amcharts4/maps.js"></script>
<script src="assets/map/togo-regions-data.js"></script>


<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<!-- jQuery (si Bootstrap 4) -->


<!-- Inclure jQuery -->
<!--<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>-->

<!-- Inclure Bootstrap JS -->
<!--<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>-->
