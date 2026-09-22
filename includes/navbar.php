<?php
// Get current page for active class
$current_page = basename($_SERVER['PHP_SELF']);

// Determine the base path for links
$base_path = '';
if (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) {
    $base_path = '../';
} elseif (strpos($_SERVER['PHP_SELF'], '/patient/') !== false) {
    $base_path = '../';
} else {
    $base_path = '';
}
?>
<nav class="navbar">
    <div class="nav-container">
        <a href="<?php echo $base_path; ?>home.php" class="logo">
            <i class="fas fa-lungs"></i>
            <span>Meducare</span>
        </a>
        
        <button class="mobile-menu-btn" id="mobileMenuBtn">
            <i class="fas fa-bars"></i>
        </button>
        
        <div class="nav-links" id="navLinks">
            <a href="<?php echo $base_path; ?>home.php" class="<?php echo $current_page == 'home.php' ? 'active' : ''; ?>">
                <i class="fas fa-home"></i> Home
            </a>
            <a href="<?php echo $base_path; ?>medicines.php" class="<?php echo $current_page == 'medicines.php' ? 'active' : ''; ?>">
                <i class="fas fa-pills"></i> Medicines
            </a>
            <a href="<?php echo $base_path; ?>qa.php" class="<?php echo $current_page == 'qa.php' ? 'active' : ''; ?>">
                <i class="fas fa-question-circle"></i> Q&A
            </a>
            <a href="<?php echo $base_path; ?>first-aid.php" class="<?php echo $current_page == 'first-aid.php' ? 'active' : ''; ?>">
                <i class="fas fa-heartbeat"></i> First aid
            </a>
            <a href="<?php echo $base_path; ?>human-body.php" class="<?php echo $current_page == 'human-body.php' ? 'active' : ''; ?>">
                <i class="fas fa-user-md"></i> Human body
            </a>
            
            <?php if(isset($_SESSION['user_id'])): ?>
                <?php if($_SESSION['role'] == 'admin'): ?>
                    <a href="<?php echo $base_path; ?>admin/dashboard.php" class="btn-outline">
                        <i class="fas fa-tachometer-alt"></i> Admin
                    </a>
                <?php else: ?>
                    <a href="<?php echo $base_path; ?>patient/dashboard.php" class="btn-outline">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                <?php endif; ?>
                <div class="user-badge">
                    <i class="fas fa-user-circle"></i>
                    <span><?php echo htmlspecialchars($_SESSION['first_name']); ?></span>
                    <a href="<?php echo $base_path; ?>logout.php" class="logout-link" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            <?php else: ?>
                <a href="<?php echo $base_path; ?>login.php" class="btn-outline"><i class="fas fa-key"></i> Log in</a>
                <a href="<?php echo $base_path; ?>register.php" class="btn-solid"><i class="fas fa-user-plus"></i> Sign up</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<style>
/* Additional navbar styles for admin/patient dashboards */
.navbar {
    background: white;
    box-shadow: 0 4px 12px rgba(0, 30, 60, 0.04);
    position: sticky;
    top: 0;
    z-index: 1000;
    width: 100%;
}

.nav-container {
    max-width: 1300px;
    margin: 0 auto;
    padding: 1rem 2rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
}

.logo {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    font-size: 1.7rem;
    font-weight: 600;
    letter-spacing: -0.02em;
    color: #0b3b5c;
    text-decoration: none;
}

.logo i {
    color: #2d7a9b;
    font-size: 2rem;
}

.nav-links {
    display: flex;
    gap: 2rem;
    align-items: center;
    flex-wrap: wrap;
}

.nav-links a {
    text-decoration: none;
    color: #2c3e50;
    font-weight: 500;
    transition: 0.2s;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 6px;
}

.nav-links a i {
    color: #3d8c9c;
    font-size: 1.1rem;
}

.nav-links a:hover,
.nav-links a.active {
    color: #116466;
}

.nav-links a.active i {
    color: #116466;
}

.btn-outline {
    border: 1.5px solid #d0e2f2;
    background: white;
    padding: 0.5rem 1.2rem;
    border-radius: 40px;
    font-weight: 600;
    color: #1e4f6b;
    text-decoration: none;
    transition: 0.15s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-outline:hover {
    background: #ecf6fc;
    border-color: #9ac7e0;
    color: #116466;
}

.btn-solid {
    background: #116466;
    color: white;
    padding: 0.5rem 1.4rem;
    border-radius: 40px;
    font-weight: 600;
    border: none;
    text-decoration: none;
    box-shadow: 0 4px 6px rgba(0,80,90,0.15);
    transition: 0.15s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-solid:hover {
    background: #0d4f51;
    transform: translateY(-2px);
    box-shadow: 0 6px 8px rgba(0,80,90,0.2);
}

.user-badge {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #ecf6fc;
    padding: 0.3rem 1rem 0.3rem 0.5rem;
    border-radius: 40px;
}

.user-badge i {
    background: #116466;
    color: white;
    padding: 8px;
    border-radius: 50%;
    font-size: 0.9rem;
}

.user-badge span {
    font-weight: 500;
    color: #116466;
}

.logout-link {
    color: #dc3545 !important;
    padding: 0 !important;
    margin-left: 5px;
}

.mobile-menu-btn {
    display: none;
    background: none;
    border: none;
    font-size: 1.5rem;
    color: #116466;
    cursor: pointer;
}

@media (max-width: 768px) {
    .mobile-menu-btn {
        display: block;
    }
    
    .nav-links {
        display: none;
        width: 100%;
        flex-direction: column;
        gap: 1rem;
        padding: 1rem 0;
    }
    
    .nav-links.show {
        display: flex;
    }
    
    .nav-links a {
        width: 100%;
        justify-content: center;
    }
    
    .user-badge {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
// Mobile menu toggle
document.getElementById('mobileMenuBtn')?.addEventListener('click', function() {
    document.getElementById('navLinks').classList.toggle('show');
});

// Close mobile menu when clicking outside
document.addEventListener('click', function(event) {
    const navLinks = document.getElementById('navLinks');
    const mobileBtn = document.getElementById('mobileMenuBtn');
    
    if (!navLinks || !mobileBtn) return;
    
    if (!navLinks.contains(event.target) && !mobileBtn.contains(event.target)) {
        navLinks.classList.remove('show');
    }
});
</script>