@if(strtolower(Auth::user()->roles[0]->name) == "super admin")
<a href="{{route('user.createtravel')}}">
    <button type="button" class="btn btn-info"><i class="fa fa-plus-circle"></i> Create Travel User </button>
</a>
<a href="{{route('user.createvisaprovider')}}">
    <button type="button" class="btn btn-info"><i class="fa fa-plus-circle"></i> Create Provider Visa User </button>
</a>
<a href="{{route('user.create')}}">
    <button type="button" class="btn btn-info"><i class="fa fa-plus-circle"></i> Create User</button>
</a>
@elseif(strtolower(Auth::user()->roles[0]->name) == "visa provider")
<a href="{{route('user.createvisaprovider')}}">
    <button type="button" class="btn btn-info"><i class="fa fa-plus-circle"></i> Create Provider Visa User </button>
</a>
@else
<a href="{{route('user.createtravel')}}">
    <button type="button" class="btn btn-info"><i class="fa fa-plus-circle"></i> Create Travel User </button>
</a>
@endif