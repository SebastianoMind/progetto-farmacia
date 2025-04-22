

<header class="mb-auto">
        <div>
            <h3 class="float-md-start mb-0">CMS</h3>
            <nav class="nav nav-masthead justify-content-center ">
            <a class="nav-link <?= ($page == 'index.php') ? 'active' : ''; ?> " aria-current="page" href="index.php">Home</a>
            <a class="nav-link <?= ($page == 'farmaci.php' || $page == 'farmaco.php' ) ? 'active' : ''; ?> " href="farmaci.php">Farmaci</a>
            <a class="nav-link" href="#">Principi Attivi</a>
            <a class="nav-link" href="#">Aziende Produttrici</a>

             <?php if(isset($_SESSION['login']) && $_SESSION['login'] === true){
             echo  ' <a class="nav-link" href="logout.php">Logout</a>'; } ?>
          </nav>


           <!--  <div class="nav nav-masthead  float-md-end">
                <button type="button" class="btn btn-outline-primary me-2">Login</button>
                <button type="button" class="btn btn-primary">Sign-up</button>
            </div> -->
        </div>
      
      </header> 