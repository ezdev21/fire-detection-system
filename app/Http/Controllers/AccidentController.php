<?php

namespace App\Http\Controllers;

use App\Events\NewAccident;
use App\Http\Requests\StoreAccidentRequest;
use App\Http\Requests\UpdateAccidentRequest;
use App\Models\Accident;
use Carbon\Carbon;

class AccidentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $accidents=Accident::where('completed_at',null)->where('removed_at',null)->get();
        foreach ($accidents as $accident) {
          $accident->created_time = $accident->created_at->diffForHumans();
          $accident->src="https://maps.google.com/maps/embed/$accident->latitude,$accident->longitude&hl=es&z=15&amp;output=embed;";
        }
        return $accidents;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAccidentRequest $request)
    {
        $accident = new Accident();
        $accident->latitude = $request->latitude;
        $accident->longitude = $request->longitude;
        $accident->save();
        broadcast(new NewAccident($accident));
        return response()->json(['message'=>'new fire accident reported successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Accident $accident)
    {
        $accident->created_time = $accident->created_at->diffForHumans();
        $accident->src="https://maps.google.com/maps/embed/$accident->latitude,$accident->longitude&hl=es&z=15&amp;output=embed;";
        $accident->broadcasted=false;
        return response()->json($accident);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Accident $accident)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAccidentRequest $request, Accident $accident)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Accident $accident)
    {
        $accident->delete();
        return response()->json(['message','fire accident removed succesfully']);
    }

    public function complete(Accident $accident)
    {
      $accident->completed_at=now();
      $accident->save();
      return response()->json(['message','fire accident resolved successfully']);
    }

    public function statistics()
    {
        $accidents=Accident::whereNot('completed_at',null)->get();
        $data=[];
        foreach ($accidents as $accident) {
           $timeToComplete= Carbon::parse($accident->completed_at)->diffInMinutes(Carbon::parse($accident->created_at));
           array_push($data,$timeToComplete);
        }
        $resolvedInOneHour=0;
        $resolvedInTwoHours=0;
        $resolvedInThreeHours=0;
        $resolvedInMoreThanThreeHours=0;
        foreach ($data as $element) {
            if($element<=60){
                $resolvedInOneHour++;
            }
            elseif($element>60&&$element<=120){
                $resolvedInTwoHours++;
            }
            elseif($element>120&&$element<=180){
                $resolvedInThreeHours++;
            }
            else{
                $resolvedInMoreThanThreeHours++;
            }
        }
        return [
            'data'=>[
                'resolvedInOneHour'=>$resolvedInOneHour,
                'resolvedInTwoHours'=>$resolvedInTwoHours,
                'resolvedInThreeHours'=>$resolvedInThreeHours,
                'resolvedInMoreThanThreeHours'=>$resolvedInMoreThanThreeHours,
            ],
            'resolvedInOneHourPercent'=>($resolvedInOneHour/$accidents->count())*100,
            'resolvedInTwoHoursPercent'=>($resolvedInTwoHours/$accidents->count())*100,
            'resolvedInThreeHoursPercent'=>($resolvedInThreeHours/$accidents->count())*100,
            'resolvedInMoreThanThreeHoursPercent'=>($resolvedInMoreThanThreeHours/$accidents->count())*100,
        ];       
    }
}
