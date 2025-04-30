<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100" style="background-color: #007bffa1 !important">
  <style>
    body {
      /* background: url("images/fintech_2.png") no-repeat;
      background-size: contain; */
      background-color: #007bffa1 !important;
    }

  </style>
  <div>
    {{ $logo }}
  </div>

  <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
    {{ $slot }}
  </div>
</div>
