<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
?>
<nav>
  <div class="nav">
    <div class="nav-section">
      <img src="/img/image-brand.png">  
        <div class="menu">
            <ul>
                <li><a href="/myMusic">My Music</a></li>
                <li><a href="/index">Reproductor</a></li>
                <li><a href="/premium">Premiun</a></li>
                <li>
                  <?php
                      if(isset($_SESSION['email'])){
                        echo '<a href="perfil"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" class="size-6" width="40" height="40">
                                                  <defs>
                                                    <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="0%">
                                                      <stop offset="20%" stop-color="#481B9A" />
                                                      <stop offset="100%" stop-color="#FF4EC4" />
                                                    </linearGradient>
                                                  </defs>
                                                  <path stroke="url(#grad)" fill="none" stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                </svg></a>
                                             
                            ';
                      }else{
                        echo "<button><a href='/login'>Sign in</a></button>";
                        echo "<button><a href='/register'>Register</a></button>";
                      }
                  ?>
                </li>
            </ul>
        </div>
    </div>
  </div>
</nav>
<!-- <div class="bottom-fade">
  <img src="/img/degradado1.png">
</div>
 -->