<?php

use Illuminate\Database\Seeder;
use App\Slider;
use Carbon\Carbon;

class SliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $faker = Faker::create();
        $imgs = [
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1577546657/maxresdefault_zbhu9s.jpg",
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1577546692/doggy-day-care_zjjlau.jpg",
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1577546693/Document_bfwsws.jpg",
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1577546696/20190530_092328_dxtsnz.jpg",
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1577546700/abp-008_ndoxa0.png",
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1577546705/doggie-daycare-1-612x250_w66qb8.jpg",
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1577546711/Brady-and-Chess-for-CL_lxhwcq.jpg",
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1577546720/german-shepherd-248622-1920_pzu7by.jpg",
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1577546727/6741447-dog-of-breed-the-griffon-bruxellois-after-grooming-the-doggie-is-dressed-in-a-striped-vest_oei5fz.jpg",
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1577546735/51cHSmiDkhL._AC_SY355__j8ho3h.jpg",
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1577546757/5377_57_rrau2q.jpg",
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1577546769/ENbnKHpAo18GJVST4dC8BDiVmqxzGtIPP14pHOeQhJrXFR4YV125RZHyRvPPLc6DD9-8YXK6_w1080-h608-p-no-v0_azajcm.jpg",
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1577546782/823b8c_613b0ee90bb9436e871600bed5741373_mv2_dkwhqb.webp",
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1577546796/doggie-school-bus_oduanr.jpg",
        ];

        for($x=0 ; $x<5 ; $x++){
            $slider = new Slider();
            $slider->uid = Carbon::now()->timestamp. (Slider::count()+1);
            $slider->page = 'shop';
            $slider->imgpath = $imgs[$x];
            $slider->save();
        }
    }
}
