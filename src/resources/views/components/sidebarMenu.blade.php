<li class="tm {{($active??false)?'nav-active active':''}} {{($subMenus??false)===false?'':' nav-parent'}}">
    <a href="{{$href??'#'}}"><i class="fa fa-{{$icon??'angle-right'}}"></i><span>{{$slot??''}}</span></a>
    @if(($subMenus??false))
    <ul class="children collapse">
        <!-- <li><a href="submenu1.html">Submenu 1</a></li> -->
        {{$subMenus}}
    </ul>
    @endif
</li>