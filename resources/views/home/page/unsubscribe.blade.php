<div class="container">
    @if($status == 1)
        <h3>{{__('home_page.unsubscribe_success')}}</h3>
    @else
        <h3>{{__('home_page.unsubscribe_not_success')}}</h3>
    @endif
        <h5>{{__('home_page.unsubscribe_message')}}</h5>
</div>
