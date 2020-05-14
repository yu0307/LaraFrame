<li class="tm {{($active??false)?'nav-active active':''}} {{($subMenus??false)===false?'':' nav-parent'}}">
    <a href="{{$href??'#'}}"><i {{ $attributes->merge(['class' => 'fa fa-'.$icon]) }}></i><span>{{$slot??''}}</span></a>
    @if(($subMenus??false))
    <ul class="children collapse">
        <!-- <li><a href="submenu1.html">Submenu 1</a></li> -->
        {{$subMenus}}
    </ul>
    @endif
</li>