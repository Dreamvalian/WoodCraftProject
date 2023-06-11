<?php

session_start();
@include('../server/connection.php');

if (!isset($_SESSION['logged_in'])) {
  header('location: ../pages/login.html');
  exit;
}

if (isset($_GET['logout'])) {
  if (isset($_SESSION['logged_in'])) {
    unset($_SESSION['logged_in']);
    unset($_SESSION['user_id']);
    unset($_SESSION['user_name']);
    unset($_SESSION['user_email']);
    unset($_SESSION['user_address']);
    unset($_SESSION['member_date']);
    unset($_SESSION['user_role']);
    header('location: ../pages/login.html');
    exit;
  } else {
    echo "Session logged_in tidak ditemukan.";
  }
}

$query_customer = "SELECT username, grandtotal, payment_status, order_date from user inner join
pemesanan on user.user_id = pemesanan.user_id";

//for display user data
$stmt_customer = $conn->prepare($query_customer);
$stmt_customer->execute();
$customer = $stmt_customer->get_result();

//for display how much user user that makes and order
$count_customer = "SELECT COUNT(order_id) AS customer_count FROM pemesanan";
$stmt_count = $conn->prepare($count_customer);
$stmt_count->execute();
$count = $stmt_count->get_result()->fetch_assoc();
$customerCount = $count['customer_count'];

//for sum grandtotal
$sum_customer = "SELECT sum(grandtotal) AS customer_sum from pemesanan";
$stmt_sum = $conn->prepare($sum_customer);
$stmt_sum->execute();
$sum = $stmt_sum->get_result()->fetch_assoc();
$customerSum = $sum['customer_sum'];

//for new customers
$new_customer = "SELECT COUNT(user_id) as new_customer from user WHERE role like '%customer%'";
$stmt_new_customer = $conn->prepare($new_customer);
$stmt_new_customer->execute();
$new = $stmt_new_customer->get_result()->fetch_assoc();
$newCustomer = $new['new_customer'];



?>

<!DOCTYPE html>
<html :class="{ 'theme-dark': dark }" x-data="data()" lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="./assets/css/tailwind.output.css" />
  <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
  <script src="./assets/js/init-alpine.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js" defer></script>
  <script src="./assets/js/charts-lines.js" defer></script>
  <script src="./assets/js/charts-pie.js" defer></script>
</head>

<body>
  <div class="flex h-screen bg-gray-50 dark:bg-gray-900" :class="{ 'overflow-hidden': isSideMenuOpen }">
    <!-- Desktop sidebar -->
    <aside class="z-20 hidden w-64 overflow-y-auto bg-white dark:bg-gray-800 md:block flex-shrink-0">
      <div class="py-4 text-gray-500 dark:text-gray-400">
        <a class="ml-6 text-lg font-bold text-gray-800 dark:text-gray-200" href="index.php">
          Wood Craft
        </a>
        <ul class="mt-6">
          <li class="relative px-6 py-3">
            <span class="absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
            <a class="inline-flex items-center w-full text-sm font-semibold text-gray-800 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100" href="index.php">
              <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
              </svg>
              <span class="ml-4">Dashboard</span>
            </a>
          </li>
        </ul>
        <ul>
          <li class="relative px-6 py-3">
            <a class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200" href="tables.php">
              <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                <path d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
              </svg>
              <span class="ml-4">Tables Products</span>
            </a>
          </li>
        </ul>
        <ul>
          <li class="relative px-6 py-3">
            <a class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200" href="create-product.php">
              <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
              </svg>
              <span class="ml-4">Create Product</span>
              <span class="ml-2" aria-hidden="true">+</span>
            </a>
          </li>
        </ul>
    </aside>
    <!-- Mobile sidebar -->
    <!-- Backdrop -->
    <div x-show="isSideMenuOpen" x-transition:enter="transition ease-in-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in-out duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-10 flex items-end bg-black bg-opacity-50 sm:items-center sm:justify-center"></div>
    <aside class="fixed inset-y-0 z-20 flex-shrink-0 w-64 mt-16 overflow-y-auto bg-white dark:bg-gray-800 md:hidden" x-show="isSideMenuOpen" x-transition:enter="transition ease-in-out duration-150" x-transition:enter-start="opacity-0 transform -translate-x-20" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in-out duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 transform -translate-x-20" @click.away="closeSideMenu" @keydown.escape="closeSideMenu">
      <div class="py-4 text-gray-500 dark:text-gray-400">
        <a class="ml-6 text-lg font-bold text-gray-800 dark:text-gray-200" href="#">
          Windmill
        </a>
        <ul class="mt-6">
          <li class="relative px-6 py-3">
            <span class="absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
            <a class="inline-flex items-center w-full text-sm font-semibold text-gray-800 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100" href="index.php">
              <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
              </svg>
              <span class="ml-4">Dashboard</span>
            </a>
          </li>
        </ul>
        <ul>
          <li class="relative px-6 py-3">
            <a class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200" href="tables.php">
              <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                <path d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
              </svg>
              <span class="ml-4">Tables Products</span>
            </a>
          </li>
          <li class="relative px-6 py-3">
            <a class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200" href="create-product.php">
              <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
              </svg>
              <span class="ml-4">Create Product</span>
              <span class="ml-2" aria-hidden="true">+</span>
            </a>
          </li>
        </ul>
      </div>
    </aside>
    <div class="flex flex-col flex-1 w-full">
      <header class="z-10 py-4 bg-white shadow-md dark:bg-gray-800">
        <div class="container flex items-center justify-between h-full px-6 mx-auto text-purple-600 dark:text-purple-300">
          <!-- Mobile hamburger -->
          <button class="p-1 mr-5 -ml-1 rounded-md md:hidden focus:outline-none focus:shadow-outline-purple" @click="toggleSideMenu" aria-label="Menu">
            <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
            </svg>
          </button>
          <!-- Search input -->
          <div class="flex justify-center flex-1 lg:mr-32">
            <div class="relative w-full max-w-xl mr-6 focus-within:text-purple-500">
              <div class="absolute inset-y-0 flex items-center pl-2">
                <svg class="w-4 h-4" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                </svg>
              </div>
              <form action="admin-finder.php" method="GET">
                <input class="w-full pl-8 pr-2 text-sm text-gray-700 placeholder-gray-600 bg-gray-100 border-0 rounded-md dark:placeholder-gray-500 dark:focus:shadow-outline-gray dark:focus:placeholder-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:placeholder-gray-500 focus:bg-white focus:border-purple-300 focus:outline-none focus:shadow-outline-purple form-input" type="text" placeholder="Search for customers" aria-label="Search" name="searchInput" />
              </form>
            </div>
          </div>
          <ul class="flex items-center flex-shrink-0 space-x-6">
            <!-- Theme toggler -->
            <li class="flex">
              <button class="rounded-md focus:outline-none focus:shadow-outline-purple" @click="toggleTheme" aria-label="Toggle color mode">
                <template x-if="!dark">
                  <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                  </svg>
                </template>
                <template x-if="dark">
                  <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                  </svg>
                </template>
              </button>
            </li>
            <!-- Notifications menu -->
            <li class="relative">
              <button class="relative align-middle rounded-md focus:outline-none focus:shadow-outline-purple" @click="toggleNotificationsMenu" @keydown.escape="closeNotificationsMenu" aria-label="Notifications" aria-haspopup="true">
                <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                </svg>
                <!-- Notification badge -->
                <span aria-hidden="true" class="absolute top-0 right-0 inline-block w-3 h-3 transform translate-x-1 -translate-y-1 bg-red-600 border-2 border-white rounded-full dark:border-gray-800"></span>
              </button>
              <template x-if="isNotificationsMenuOpen">
                <ul x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click.away="closeNotificationsMenu" @keydown.escape="closeNotificationsMenu" class="absolute right-0 w-56 p-2 mt-2 space-y-2 text-gray-600 bg-white border border-gray-100 rounded-md shadow-md dark:text-gray-300 dark:border-gray-700 dark:bg-gray-700">
                  <li class="flex">
                    <a class="inline-flex items-center justify-between w-full px-2 py-1 text-sm font-semibold transition-colors duration-150 rounded-md hover:bg-gray-100 hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-gray-200" href="#">
                      <span>Messages</span>
                      <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-600 bg-red-100 rounded-full dark:text-red-100 dark:bg-red-600">
                        13
                      </span>
                    </a>
                  </li>
                  <li class="flex">
                    <a class="inline-flex items-center justify-between w-full px-2 py-1 text-sm font-semibold transition-colors duration-150 rounded-md hover:bg-gray-100 hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-gray-200" href="#">
                      <span>Sales</span>
                      <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-600 bg-red-100 rounded-full dark:text-red-100 dark:bg-red-600">
                        2
                      </span>
                    </a>
                  </li>
                  <li class="flex">
                    <a class="inline-flex items-center justify-between w-full px-2 py-1 text-sm font-semibold transition-colors duration-150 rounded-md hover:bg-gray-100 hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-gray-200" href="#">
                      <span>Alerts</span>
                    </a>
                  </li>
                </ul>
              </template>
            </li>
            <!-- Profile menu -->
            <li class="relative">
              <button class="align-middle rounded-full focus:shadow-outline-purple focus:outline-none" @click="toggleProfileMenu" @keydown.escape="closeProfileMenu" aria-label="Account" aria-haspopup="true">
                <img class="object-cover w-8 h-8 rounded-full" src="https://images.unsplash.com/photo-1502378735452-bc7d86632805?ixlib=rb-0.3.5&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=200&fit=max&s=aa3a807e1bbdfd4364d1f449eaa96d82" alt="" aria-hidden="true" />
              </button>
              <template x-if="isProfileMenuOpen">
                <ul x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click.away="closeProfileMenu" @keydown.escape="closeProfileMenu" class="absolute right-0 w-56 p-2 mt-2 space-y-2 text-gray-600 bg-white border border-gray-100 rounded-md shadow-md dark:border-gray-700 dark:text-gray-300 dark:bg-gray-700" aria-label="submenu">
                  <li class="flex">
                    <a class="inline-flex items-center w-full px-2 py-1 text-sm font-semibold transition-colors duration-150 rounded-md hover:bg-gray-100 hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-gray-200" href="index.php?logout=1">
                      <svg class="w-4 h-4 mr-3" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                      </svg>
                      <span>Log out</span>
                    </a>
                  </li>
                </ul>
              </template>
            </li>
          </ul>
        </div>
      </header>
      <main class="h-full overflow-y-auto">
        <div class="container px-6 mx-auto grid">
          <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Dashboard
          </h2>
          <!-- Cards -->
          <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4">
            <!-- Card -->
            <?php foreach ($count as $count) { ?>
              <div class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
                <div class="p-3 mr-4 text-orange-500 bg-orange-100 rounded-full dark:text-orange-100 dark:bg-orange-500">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"></path>
                  </svg>
                </div>
                <div>
                  <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                    Total Orders
                  </p>
                  <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                    <?php echo $customerCount; ?>
                  </p>
                </div>
              <?php } ?>
              </div>
              <!-- Card -->
              <?php foreach ($sum as $sum) { ?>
                <div class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
                  <div class="p-3 mr-4 text-green-500 bg-green-100 rounded-full dark:text-green-100 dark:bg-green-500">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                    </svg>
                  </div>
                  <div>
                    <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                      Account balance
                    </p>
                    <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                      Rp. <?php echo $customerSum; ?>
                    </p>
                  </div>
                <?php } ?>
                </div>
                <!-- Card -->
                <div class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
                  <div class="p-3 mr-4 text-blue-500 bg-blue-100 rounded-full dark:text-blue-100 dark:bg-blue-500">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path>
                    </svg>
                  </div>
                  <div>
                    <?php foreach ($new as $new) { ?>
                      <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                        Customers
                      </p>
                      <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                        <?php echo $newCustomer; ?>
                      </p>
                    <?php } ?>
                  </div>
                </div>
          </div>
          <!-- New Table -->
          <div class="w-full overflow-hidden rounded-lg shadow-xs">
            <div class="w-full overflow-x-auto">
              <table class="w-full whitespace-no-wrap">
                <thead>
                  <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                    <th class="px-4 py-3">Customer</th>
                    <th class="px-4 py-3">Amount</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Date</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                  <?php foreach ($customer as $customer) { ?>
                    <tr class="text-gray-700 dark:text-gray-400">
                      <td class="px-4 py-3">
                        <div class="flex items-center text-sm">
                          <!-- Avatar with inset shadow -->
                          <div class="relative hidden w-8 h-8 mr-3 rounded-full md:block">
                            <img class="object-cover w-full h-full rounded-full" src="https://images.unsplash.com/flagged/photo-1570612861542-284f4c12e75f?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=200&fit=max&ixid=eyJhcHBfaWQiOjE3Nzg0fQ" alt="" loading="lazy" />
                            <div class="absolute inset-0 rounded-full shadow-inner" aria-hidden="true"></div>
                          </div>
                          <div>
                            <p class="font-semibold"><?php echo $customer['username']; ?></p>
                          </div>
                        </div>
                      </td>
                      <td class="px-4 py-3 text-sm">
                        Rp. <?php echo $customer['grandtotal']; ?>
                      </td>
                      <td class="px-4 py-3 text-xs">
                        <span class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full dark:bg-green-700 dark:text-green-100">
                          <?php echo $customer['payment_status']; ?>
                        </span>
                      </td>
                      <td class="px-4 py-3 text-sm">
                        <?php echo $customer['order_date']; ?>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>

          </div>
      </main>
    </div>
  </div>
</body>

</html>