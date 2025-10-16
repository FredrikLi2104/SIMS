<body class="vertical-layout vertical-menu-modern {{ $configData['verticalMenuNavbarType'] }} {{ $configData['blankPageClass'] }} {{ $configData['bodyClass'] }} {{ $configData['sidebarClass'] }} {{ $configData['footerType'] }} {{ $configData['contentLayout'] }}" data-open="click" data-menu="vertical-menu-modern" data-col="{{ $configData['showMenu'] ? $configData['contentLayout'] : '1-column' }}" data-framework="laravel" data-asset-path="{{ asset('/') }}">
    <!-- BEGIN: Header-->
    @include('panels.navbar')
    <!-- END: Header-->

    <!-- BEGIN: Main Menu-->
    @if (isset($configData['showMenu']) && $configData['showMenu'] === true)
        @include('panels.sidebar')
    @endif
    <!-- END: Main Menu-->

    <!-- BEGIN: Content-->
    <div class="app-content content {{ $configData['pageClass'] }}">
        <!-- BEGIN: Header-->
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>

        @if ($configData['contentLayout'] !== 'default' && isset($configData['contentLayout']))
            <div class="content-area-wrapper {{ $configData['layoutWidth'] === 'boxed' ? 'container-xxl p-0' : '' }}">
                <div class="{{ $configData['sidebarPositionClass'] }}">
                    <div class="sidebar">
                        {{-- Include Sidebar Content --}}
                        @yield('content-sidebar')
                    </div>
                </div>
                <div class="{{ $configData['contentsidebarClass'] }}">
                    <div class="content-wrapper">
                        <div class="content-body" id="app">
                            {{-- Include Page Content --}}
                            @yield('content')
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="content-wrapper {{ $configData['layoutWidth'] === 'boxed' ? 'container-xxl p-0' : '' }}">
                {{-- Include Breadcrumb --}}
                @if ($configData['pageHeader'] === true && isset($configData['pageHeader']))
                    @include('panels.breadcrumb')
                @endif

                <div class="content-body" id="app">
                    {{-- Include Page Content --}}
                    @yield('content')
                </div>
            </div>
        @endif

    </div>
    <!-- End: Content-->

    @if ($configData['blankPage'] == false && isset($configData['blankPage']))
    @endif

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    {{-- include footer --}}
    @include('panels/footer')

    {{-- include GDPR chatbot with LLM --}}
    @auth
        @include('components.gdpr-chatbot')
    @endauth

    {{-- include default scripts --}}
    @include('panels/scripts')

    {{-- include chatbot script with LLM --}}
    @auth
        <script src="{{ asset('js/gdpr-chatbot.js') }}"></script>
    @endauth

    <script type="text/javascript">
        let x = $('html');
        console.log(x.hasClass('dark-layout'));
        $(window).on('load', function() {
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }
        })
    </script>
    <!--
    <script type='text/javascript'>
        function setSession(x, token) {
          let load = new FormData();
          load.append('theme', x);
            let url = "/set-session";
            let r = new XMLHttpRequest();
            r.open('POST', url, true);
            r.setRequestHeader('X-CSRF-TOKEN', token);
            r.send(load);
            r.onload = function() {
                console.log(r);
            }
        }
    </script>
  -->
</body>

</html>
