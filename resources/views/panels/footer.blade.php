<!-- BEGIN: Footer-->
<footer class="footer footer-light {{($configData['footerType'] === 'footer-hidden') ? 'd-none':''}} {{$configData['footerType']}}">
  <p class="clearfix mb-0">
    <span class="float-md-start d-block d-md-inline-block mt-25">@lang('messages.copyright') &copy;
      <script>document.write(new Date().getFullYear())</script><a class="ms-25" href="https://gdpr.se" target="_blank">GDPR</a>,
      <span class="d-none d-sm-inline-block">@lang('messages.allRightsReserved')</span>
    </span>
    <span class="float-md-end d-none d-md-block">@lang('messages.handCrafted')<i data-feather="heart"></i></span>
  </p>
</footer>
<button class="btn btn-primary btn-icon scroll-top" type="button"><i data-feather="arrow-up"></i></button>
<!-- END: Footer-->
