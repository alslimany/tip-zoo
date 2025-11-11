<div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Opening Hours</h5>
        </div>
        <div class="card-body">
            @foreach($hours as $day => $schedule)
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label>{{ $day }}</label>
                    </div>
                    <div class="col-md-3">
                        <input type="time" class="form-control" wire:model="hours.{{ $day }}.open" 
                               @if($schedule['closed']) disabled @endif>
                    </div>
                    <div class="col-md-3">
                        <input type="time" class="form-control" wire:model="hours.{{ $day }}.close" 
                               @if($schedule['closed']) disabled @endif>
                    </div>
                    <div class="col-md-3">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" 
                                   id="closed_{{ $day }}" wire:model="hours.{{ $day }}.closed">
                            <label class="custom-control-label" for="closed_{{ $day }}">Closed</label>
                        </div>
                    </div>
                </div>
                <!-- Hidden inputs to pass data to form -->
                <input type="hidden" name="opening_hours[{{ $day }}][open]" value="{{ $schedule['open'] }}">
                <input type="hidden" name="opening_hours[{{ $day }}][close]" value="{{ $schedule['close'] }}">
                <input type="hidden" name="opening_hours[{{ $day }}][closed]" value="{{ $schedule['closed'] ? '1' : '0' }}">
            @endforeach
        </div>
    </div>
</div>
