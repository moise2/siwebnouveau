<!-- Sidebar -->
<nav class="sidebar-nav mt-5" id="sidebar">
    <p></p>
    <div class="pt-8 mb-12">
       <h4 class="text-white"></h4>
    </div>
    <div class="pt-8 mb-12">
       <h4 class="text-white"></h4>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link text-white" href="#">
                <i class='bx bxs-dashboard'></i>
                <span>Vue d'ensemble</span>
            </a>
        </li>
        

        <hr class="bg-light my-3">
        
        <li class="nav-item">
            <form action="{{ route('voyager.logout') }}" method="POST" id="logout-form">
                @csrf
                <a class="nav-link text-white" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class='bx bx-log-out'></i>
                    <span>Déconnexion</span>
                </a>
            </form>
        </li>
    </ul>
</nav>