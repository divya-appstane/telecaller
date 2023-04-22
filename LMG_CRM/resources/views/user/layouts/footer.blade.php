                        <!-- Footer START -->
                    <div class="footer">
                        <div class="footer-content">
                            <p class="mb-0">Copyright © {{date('Y')}} LetmeGrab e-Platform Pvt. Ltd. All rights reserved.</p>
                            <span>
                                <a href="" class="text-gray me-3">Term &amp; Conditions</a>
                                <a href="" class="text-gray">Privacy &amp; Policy</a>
                            </span>
                        </div>
                    </div>
                    <!-- Footer End -->
                </div>
                <!-- Content END -->
            
            </div>
        </div>


        <!-- Core Vendors JS -->
        <script src="{{env('USER_ASSETS')}}js/vendors.min.js"></script>

        <!-- page js -->
        @stack('scripts')

        <!-- Core JS -->
        <script src="{{env('USER_ASSETS')}}js/app.min.js"></script>

        <!-- Sweetalert2 JS (CDN)-->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            const swalModelTimeOut = 2500;
            const pageReloadTimeOut = 3000;
        </script>

    </body>

</html>