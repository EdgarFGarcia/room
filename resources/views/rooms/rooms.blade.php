@foreach($roomGroup as $group)
  <?php
    $groupname = $group->group_name;
    $rtypes = explode(',', $group->roomtype);
  ?>
  <div class="col-md-4">
    <div class="col-md-12" style="text-align: center;">
      <h1 style="margin-bottom: 15px;">{{ $groupname }}</h1>
      @foreach($query as $rooms)
        @if(array_search($rooms->room_type_id, $rtypes)!==false)
        <div class="tooltips">
        <span style="font-size: 12px;" id="tooltips{{$rooms->id}}" class="tooltiptext">{{$rooms->room_status}} @if($rooms->is_name == "1") By {{$rooms->userinfo}} @endif</span>
            <div id="div{{ $rooms->id }}"
             @if($rooms->is_blink == "1")
              class="col-md-12 blink_me row"
             @else
              class="col-md-12 row"
             @endif
            style="">
              
              <button 
                type="button" 
                class="btn btn-flat" 
                id="roomid{{$rooms->id}}" 
                name="roomid[]" 
                value="{{$rooms->id}}" 
                data-placement="top"
                @if($rooms->from_room_status_id=="66")
                style="height: 85px; background: pink;"                
                @else
                style="height: 85px; background: {{$rooms->color}};"
                @endif
              >

                
                <table style="width: 100%;"> 
                  <tr>
                    @if($rooms->from_room_status_id=="66")
                      <td><img id="imageStatus{{$rooms->id}}" src="{{asset('images/statuses/status_inspectrecovery.png')}}" style="max-width: 40px;"/></td>
                    @else
                      <td><img id="imageStatus{{$rooms->id}}" src="{{asset('images/statuses/status_')}}{{$rooms->room_status_id}}.png" style="max-width: 40px;"/></td>
                    @endif
                  </tr>
                  <tr id="timer{{ $rooms->id }}">
                    @if($rooms->is_timer=="1")
                      <script>Timer("{{ $rooms->id }}");</script>
                      <td><label style="font-size: 9px; color: black;">00:00</label></td>
                    @else
                      <td></td>
                    @endif
                  </tr> 
                </table>

              </button>
                
                 <table style="text-align: center; margin: 5px;">
                  <tr>
                    <td style="font-size: 32px; font-style: bold;"><strong>{{ $rooms->room_name }}</strong></td>
                  </tr>
                </table>
                   
              </div> 
            </div>
        @endif
      @endforeach
    </div>
  </div>
@endforeach