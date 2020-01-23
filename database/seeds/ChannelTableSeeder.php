<?php

use Illuminate\Database\Seeder;
use App\Channel;
use App\Video;
use App\Comment;
use App\SecondComment;
use Faker\Factory as Faker;
use App\Company;
use App\User;
use Carbon\Carbon;


class ChannelTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $faker = Faker::create();
        $videolinks = [
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879185/y2mate.com_-_314_1_6QMk-GBOta0_360p_jxkxiq.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879167/y2mate.com_-_3142_R9sgsZe5XZ0_240p_rgh5wd.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879154/y2mate.com_-_2019_hd_2019_XbYTjVfjDeU_360p_u3tfnx.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879152/y2mate.com_-_047__IW-vXpT0K-k_360p_tjgfuu.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879185/y2mate.com_-_314_1_6QMk-GBOta0_360p_jxkxiq.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879167/y2mate.com_-_3142_R9sgsZe5XZ0_240p_rgh5wd.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879154/y2mate.com_-_2019_hd_2019_XbYTjVfjDeU_360p_u3tfnx.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879152/y2mate.com_-_047__IW-vXpT0K-k_360p_tjgfuu.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879185/y2mate.com_-_314_1_6QMk-GBOta0_360p_jxkxiq.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879167/y2mate.com_-_3142_R9sgsZe5XZ0_240p_rgh5wd.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879154/y2mate.com_-_2019_hd_2019_XbYTjVfjDeU_360p_u3tfnx.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879152/y2mate.com_-_047__IW-vXpT0K-k_360p_tjgfuu.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879185/y2mate.com_-_314_1_6QMk-GBOta0_360p_jxkxiq.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879167/y2mate.com_-_3142_R9sgsZe5XZ0_240p_rgh5wd.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879154/y2mate.com_-_2019_hd_2019_XbYTjVfjDeU_360p_u3tfnx.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879152/y2mate.com_-_047__IW-vXpT0K-k_360p_tjgfuu.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879185/y2mate.com_-_314_1_6QMk-GBOta0_360p_jxkxiq.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879167/y2mate.com_-_3142_R9sgsZe5XZ0_240p_rgh5wd.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879154/y2mate.com_-_2019_hd_2019_XbYTjVfjDeU_360p_u3tfnx.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879152/y2mate.com_-_047__IW-vXpT0K-k_360p_tjgfuu.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879185/y2mate.com_-_314_1_6QMk-GBOta0_360p_jxkxiq.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879167/y2mate.com_-_3142_R9sgsZe5XZ0_240p_rgh5wd.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879154/y2mate.com_-_2019_hd_2019_XbYTjVfjDeU_360p_u3tfnx.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879152/y2mate.com_-_047__IW-vXpT0K-k_360p_tjgfuu.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879185/y2mate.com_-_314_1_6QMk-GBOta0_360p_jxkxiq.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879167/y2mate.com_-_3142_R9sgsZe5XZ0_240p_rgh5wd.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879154/y2mate.com_-_2019_hd_2019_XbYTjVfjDeU_360p_u3tfnx.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879152/y2mate.com_-_047__IW-vXpT0K-k_360p_tjgfuu.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879185/y2mate.com_-_314_1_6QMk-GBOta0_360p_jxkxiq.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879167/y2mate.com_-_3142_R9sgsZe5XZ0_240p_rgh5wd.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879154/y2mate.com_-_2019_hd_2019_XbYTjVfjDeU_360p_u3tfnx.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879152/y2mate.com_-_047__IW-vXpT0K-k_360p_tjgfuu.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879185/y2mate.com_-_314_1_6QMk-GBOta0_360p_jxkxiq.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879167/y2mate.com_-_3142_R9sgsZe5XZ0_240p_rgh5wd.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879154/y2mate.com_-_2019_hd_2019_XbYTjVfjDeU_360p_u3tfnx.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879152/y2mate.com_-_047__IW-vXpT0K-k_360p_tjgfuu.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879185/y2mate.com_-_314_1_6QMk-GBOta0_360p_jxkxiq.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879167/y2mate.com_-_3142_R9sgsZe5XZ0_240p_rgh5wd.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879154/y2mate.com_-_2019_hd_2019_XbYTjVfjDeU_360p_u3tfnx.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879152/y2mate.com_-_047__IW-vXpT0K-k_360p_tjgfuu.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879185/y2mate.com_-_314_1_6QMk-GBOta0_360p_jxkxiq.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879167/y2mate.com_-_3142_R9sgsZe5XZ0_240p_rgh5wd.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879154/y2mate.com_-_2019_hd_2019_XbYTjVfjDeU_360p_u3tfnx.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879152/y2mate.com_-_047__IW-vXpT0K-k_360p_tjgfuu.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879185/y2mate.com_-_314_1_6QMk-GBOta0_360p_jxkxiq.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879167/y2mate.com_-_3142_R9sgsZe5XZ0_240p_rgh5wd.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879154/y2mate.com_-_2019_hd_2019_XbYTjVfjDeU_360p_u3tfnx.mp4",
            "https://res.cloudinary.com/dmtxkcmay/video/upload/v1575879152/y2mate.com_-_047__IW-vXpT0K-k_360p_tjgfuu.mp4",
        ];
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

        for($x=0 ; $x<10 ; $x++){
            $channel = new Channel();

            $channel->uid = Carbon::now()->timestamp. Channel::count();
            $channel->name = $faker->unique()->jobTitle;
            $channel->desc = $faker->sentence;
            $channel->email = $faker->unique()->safeEmail;
            $channel->tel1 =  $faker->ean8;
            $channel->imgpath = $imgs[$faker->randomElement([0,1,2,3,4,5,6,7,8,9,10,11,12])];
            $channel->companyBelongings = $faker->boolean();

            if($channel->companyBelongings){
                $company = Company::find($faker->randomElement([1,2,3,4,5,6,7,8,9,10,11]));
                $channel->company()->associate($company);
            }else{
                $user = User::find($faker->randomElement([1,2,3,4,5,6,7,8,9,10,11]));
                $channel->user()->associate($user);
            }

            $channel->save();

        }

        foreach($videolinks as $videolink){
            $video = new Video();
            $video->uid = Carbon::now()->timestamp . Video::count();
            $video->title = $faker->jobTitle;
            $video->desc = $faker->sentence;
            $video->videopath = $videolink;
            $video->videopublicid = Carbon::now()->timestamp;
            $video->imgpath = $imgs[$faker->randomElement([0,1,2,3,4,5,6,7,8,9])];
            $video->totallength = "10:00";
            $video->view = $faker->numberBetween($min = 1000, $max = 100000);
            $video->like =  $faker->numberBetween($min = 1000, $max = 100000);
            $video->dislike =  $faker->numberBetween($min = 1000, $max = 100000);
            $video->scope = 'public';
            $video->free = $faker->boolean();
            $video->agerestrict = false;

            if(!$video->free){
                
                $video->discbyprice = $faker->boolean();
                $video->price = $faker->numberBetween($min = 1, $max = 1000);
                $video->disc = $faker->numberBetween($min = 1, $max = 100);
                $video->discpctg = $faker->boolean($min = 0, $max = 1);
            }
            $video->channel()->associate(Channel::find($faker->randomElement([1,2,3,4,5,6,7,8,9,10])));
            $video->save();

        }

        for($z = 0 ; $z < 20 ; $z++){
            $comment = new Comment();
            $comment->uid = Carbon::now()->timestamp . Comment::count();
            $comment->text = $faker->sentence;
            $comment->type = 'video';
            $comment->like =  $faker->numberBetween($min = 1000, $max = 100000);
            $comment->dislike =  $faker->numberBetween($min = 1000, $max = 100000);

            $comment->video()->associate(Video::find(1));
            $comment->user()->associate(User::find($faker->numberBetween($min = 1, $max = 20)));
            $comment->save();
            
        }

        for($z = 0 ; $z < 10 ; $z++){
            $comment = new Comment();
            $comment->uid = Carbon::now()->timestamp . Comment::count();
            $comment->text = $faker->sentence;
            $comment->type = 'video';
            $comment->like =  $faker->numberBetween($min = 1000, $max = 100000);
            $comment->dislike =  $faker->numberBetween($min = 1000, $max = 100000);

            $comment->video()->associate(Video::find(2));
            $comment->user()->associate(User::find($faker->numberBetween($min = 1, $max = 20)));
            $comment->save();
            
        }
        
        for($z = 0 ; $z < 10 ; $z++){
            $comment = new Comment();
            $comment->uid = Carbon::now()->timestamp . Comment::count();
            $comment->text = $faker->sentence;
            $comment->type = 'video';
            $comment->like =  $faker->numberBetween($min = 1000, $max = 100000);
            $comment->dislike =  $faker->numberBetween($min = 1000, $max = 100000);

            $comment->video()->associate(Video::find(3));
            $comment->user()->associate(User::find($faker->numberBetween($min = 1, $max = 20)));
            $comment->save();
            
        }

        for($a = 0 ; $a < 50 ; $a++){
            $scomment = new SecondComment();
            $scomment->uid = Carbon::now()->timestamp . SecondComment::count();
            $scomment->text = $faker->sentence;
            $scomment->like =  $faker->numberBetween($min = 1000, $max = 100000);
            $scomment->dislike =  $faker->numberBetween($min = 1000, $max = 100000);

            $scomment->comment()->associate(Comment::find($faker->randomElement([1,2,3,4,5])));
            $scomment->user()->associate(User::find($faker->numberBetween($min = 1, $max = 20)));
            $scomment->save();
        }
    }
}
