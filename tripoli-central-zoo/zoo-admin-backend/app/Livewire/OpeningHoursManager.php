<?php

namespace App\Livewire;

use Livewire\Component;

class OpeningHoursManager extends Component
{
    public $hours = [];
    public $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

    public function mount($existingHours = [])
    {
        // Initialize with existing hours or default structure
        if (!empty($existingHours)) {
            $this->hours = $existingHours;
        } else {
            foreach ($this->days as $day) {
                $this->hours[$day] = [
                    'open' => '09:00',
                    'close' => '17:00',
                    'closed' => false,
                ];
            }
        }
    }

    public function addDay()
    {
        $this->hours['Custom'] = [
            'open' => '09:00',
            'close' => '17:00',
            'closed' => false,
        ];
    }

    public function removeDay($day)
    {
        unset($this->hours[$day]);
    }

    public function render()
    {
        return view('livewire.opening-hours-manager');
    }
}
