<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
$loggedIn = isset($_SESSION['user_id']);
$username = $loggedIn ? htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') : '';
?>
<!DOCTYPE html>
<head>
  <title>David's Digital Den - About Us</title>
  <link rel="icon" type="image/x-icon" href="/Images/logo.png">
  <center><h1 style="background-image: linear-gradient(to right, #645656, #ffffff, #ffffff, #ffffff, #645656); font-family:'Wide Latin'; text-align:center; margin: 0px;">
  David's Digital Den</h1>
  </center>
</head>

<style>
  :root {
    --bg: #4169e1;
    --content-bg: #F9EFEF;
    --edge-inset: 10px;
    --logo-size-min: 120px;
    --logo-size-pref: 14vw;
    --logo-size-max: 180px;
    --content-max: 1100px;
    --logo-size: 180px;
  }

  * { box-sizing: border-box; }
  body { margin:0; background: var(--bg); font-family: Arial, sans-serif; }

  ul { list-style:none; margin:0; padding:0; background:#333; overflow:hidden; position: sticky; top: 0; z-index: 1000;}
  li { float:left; border-right:1px solid #bbb; }
  li:last-child { float:right; border-left:1px solid #bbb; border-right:none; }
  li a { display:block; color:#fff; text-decoration:none; padding:14px 32px; text-align:center; }
  li a:hover { background:#111; }
  .active { background:#4169e1; border-left:1px solid #bbb; border-right:1px solid #bbb; border-top:1px solid #bbb; border-bottom:1px solid #bbb;}

    .dropdown { position: fixed; right: 0; }
    .dropdown-toggle { cursor: pointer; user-select: none; }
    .dropdown-menu {
      display: none;
      position: absolute;
      right: 0;
      top: 100%;
      min-width: 220px;
      background: #222;
      border: 1px solid #444;
      border-top: none;
      z-index: 2000;
    }
    .dropdown-menu li { float: none; border: none; }
    .dropdown-menu a { text-align: left; padding: 12px 16px; }
    .dropdown.open .dropdown-menu { display: block; }

  .top-wrap { 
    isolation: isolate; 
    display: grid; 
    grid-template-areas: "stack"; 
    justify-items: center; 
    align-items: stretch; 
    width:100%; 
    margin:20px 0; 
  }

  .edge-logos {
    grid-area: stack;
    width: 100vw;
    margin-inline: calc(50% - 50vw);
    position: relative;
    z-index: -1;
    pointer-events: none;
  }
  .edge-logos img {
    position: absolute;
    top: 50%;
    transform: translateY(-270%);
    width: var(--logo-size);
    height: auto;
  }
  .logo-left  { left:  var(--edge-inset); }
  .logo-right { right: var(--edge-inset); }

  .content {
    grid-area: stack;
    position: relative;
    z-index: 1;
    width: min(var(--content-max), calc(100% - 24px));
    background: var(--content-bg);
    border-radius: 10px;
    padding: 32px 24px 48px;
    text-align: left;
  }

  @media (max-width: calc(var(--content-max) + 2*(var(--logo-size-max) + var(--edge-inset)))) {
    .content {
      position: relative;
      transform: none;
      margin: 12px auto 0;
      width: min(var(--content-max), calc(100% - 24px));
      z-index: 1;
    }
  }

  @media (max-width: 1480px){
    .edge-logos{
      margin-inline: 0;
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 24px;
      position: relative;
      z-index: -1;
    }
    .edge-logos img{
      position: static;
      transform: none;
      width: var(--logo-size);
      height: auto;
    }
  }

  @media (max-width: 640px) {
    li { float:none; border-right:none; }
    li:last-child { float:none; border-left:none; }
  }

  ol li { float:none; }
</style>


<body>

<br>
  <ul>
    <li><a href="index.php">Home</a></li>
    <li><a href="products.php">Products</a></li>
    <li><a href="about.php" class="active">About Us</a></li>
    <li><a href="checkout.html">Checkout</a></li>
<?php if ($loggedIn): ?>
  <li class="right-item dropdown" id="acct-dropdown">
    <a href="#" class="dropdown-toggle" aria-haspopup="true" aria-expanded="false">
      Welcome, <?php echo $username; ?> &#9662;
    </a>
    <ul class="dropdown-menu" role="menu" aria-label="Account menu">
      <li><a href="account.html" role="menuitem">Account Information</a></li>
      <li><a href="orders.html" role="menuitem">Order History</a></li>
      <li><a href="logout.php" role="menuitem">Sign Out</a></li>
    </ul>
  </li>
<?php else: ?>
   <li class="right-item"><a href="register.php">Register</a></li>
   <li class="right-item"><a href="login.html">Log In</a></li>
<?php endif; ?>
  </ul>

<div class="top-wrap">
    <div class="edge-logos">
      <img src="/Images/logo.png" alt="Logo Left" class="logo-left">
      <img src="/Images/logo.png" alt="Logo Right" class="logo-right">
    </div>

  <div class="content">
      <center><h1><b>Welcome to David's Digital Den</b></h1></center>
      <br>
      <h4 style="padding: 0px 20px; line-height:1.5;">
        Hello! My name is David Jr., and as of writing this (September 2025), I am a senior in my ninth semester at
        Florida Gulf Coast University. I am a double major in Computer Information Systems with no concentration and
        Analytics &amp; Informatics with a Data Analytics concentration, along with a minor in Business Management. I
        created this website as a semester-long project for my CIS major capstone class, ISM 4915. I chose this topic
        because of my love for online gaming; I have been playing video games for about 80% of my life,
        since I was old enough to hold a controller and know how to use it. At 4 years old, I would sit beside my father
        and watch him play video games, and this spectation grew and flourished into a deep passion for gaming. Some of
        my favorite games include story games like The Last of Us, DOOM: Eternal, and the Wolfenstein series; chill games
        like Fallout Shelter and Dave The Diver; and online shooter games like Call of Duty and The Finals.
      </h4>
          
<br><br><br><br><br>
          
	<center>
	<h1>My Top 10 Favorite Games of All Time</h1>
	</center>
		<br><center>
	<ol style="text-align: left">
		<li>The Last of Us</li><br>
		<li>Clair Obscur: Expedition 33</li><br>
		<li>The Elder Scrolls V: Skyrim</li><br>
		<li>DOOM: Eternal</li><br>
		<li>Wolfenstein: The New Order</li><br>
		<li>Dave the Diver</li><br>
    	<li>The Finals</li><br>
    	<li>Wolfenstein: The New Colossus</li><br>
    	<li>Balatro</li><br>
    	<li>7 Days to Die</li><br>
	</ol></center>
  </div>
</div>

<script>
  (function () {
    var dd = document.getElementById('acct-dropdown');
    if (!dd) return;
    var toggle = dd.querySelector('.dropdown-toggle');
    toggle.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      dd.classList.toggle('open');
    });
    document.addEventListener('click', function () {
      dd.classList.remove('open');
    });
  })();
</script>
     
</body>
