<form action="api/notes" method="post" >
    @csrf
    {{--        <input type="hidden" value="{{$_GET['id']}}" name="id">--}}
    <input type="text"  name="id" value="3265">
    note<input type="text" name="note" value="note"><br>
    sub name<input type="text" name="subname" value="text sub name"><br>
    facility<input type="text" name="facility" value="Toronto trake"><br>
    location<input type="text" name="location" value="ontario"><br>
    meet type<input type="text" name="meetType" value="n"><br>
    sub meet type<input type="text" name="meetSubType" value="c"><br>
    season<input type="text" name="season" value="i"><br>
    startday<input type="text" name="startDay" value="2018-03-03"><br>
    endday<input type="text" name="endDay" value="2018-03-04"><br>

    {{--        <input type="email" name="emails[]">--}}
    {{--        <input type="email" name="emails[]">--}}
    {{--        <input type="email" name="emails[]">--}}

    <input type="submit" name="submit" value="submit">
</form>
