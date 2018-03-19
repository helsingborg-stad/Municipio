<div class="card" style="width: 18rem;">
  <div class="card-body">
    <h5 class="card-title">{{$title}}</h5>
    @include('utilities.date', ['date' => $date])
    <p class="card-text">{{$slot}}</p>
  </div>
</div>
