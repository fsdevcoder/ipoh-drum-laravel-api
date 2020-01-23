<?php

use Illuminate\Database\Seeder;
use App\Blogger;
use App\Article;
use App\ArticleImage;
use App\Comment;
use App\SecondComment;
use Faker\Factory as Faker;
use App\Company;
use App\User;
use Carbon\Carbon;


class BloggerTableSeeder extends Seeder
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

        for($x=0 ; $x<20 ; $x++){
            $blogger = new Blogger();

            $blogger->uid = Carbon::now()->timestamp. Blogger::count();
            $blogger->name = $faker->unique()->jobTitle;
            $blogger->desc = $faker->sentence;
            $blogger->email = $faker->unique()->safeEmail;
            $blogger->tel1 =  $faker->ean8;
            $blogger->imgpath = $imgs[$faker->randomElement([0,1,2,3,4,5,6,7,8,9,10,11,12])];
            $blogger->companyBelongings = true;

            if($blogger->companyBelongings){
                $company = Company::find($faker->randomElement([1,2,3,4,5,6,7,8,9,10,11]));
                $blogger->company()->associate($company);
            }else{
                $user = User::find($faker->randomElement([1,2,3,4,5,6,7,8,9,10,11]));
                $blogger->user()->associate($user);
            }

            $blogger->save();

        }

        for($z = 0 ; $z < 30 ; $z++){
            $article = new Article();
            $article->uid = Carbon::now()->timestamp . Article::count();
            $article->title = $faker->jobTitle;
            $article->desc = $faker->sentence;
            $article->view = $faker->numberBetween($min = 1000, $max = 100000);
            $article->like =  $faker->numberBetween($min = 1000, $max = 100000);
            $article->dislike =  $faker->numberBetween($min = 1000, $max = 100000);
            $article->scope = 'public';
            $article->agerestrict = false;
            
            $article->blogger()->associate(Blogger::find(1));
            $article->save();

        }

        for($z = 0 ; $z < 30 ; $z++){
            $articleimage = new ArticleImage();
            $articleimage->uid = Carbon::now()->timestamp . ArticleImage::count();
            $articleimage->title = $faker->jobTitle;
            $articleimage->desc = $faker->sentence;
            $articleimage->like =  $faker->numberBetween($min = 1000, $max = 100000);
            $articleimage->dislike =  $faker->numberBetween($min = 1000, $max = 100000);
            $articleimage->imgpath = $imgs[$faker->randomElement([0,1,2,3,4,5,6,7,8,9])];
            $articleimage->imgpublicid = $articleimage->uid;

            $articleimage->article()->associate(Article::find($faker->randomElement([1,2,3,4,5,6,7,8,9,10])));
            $articleimage->save();

        }

        for($z = 0 ; $z < 20 ; $z++){
            $comment = new Comment();
            $comment->uid = Carbon::now()->timestamp . Comment::count();
            $comment->text = $faker->sentence;
            $comment->type = 'article';
            $comment->like =  $faker->numberBetween($min = 1000, $max = 100000);
            $comment->dislike =  $faker->numberBetween($min = 1000, $max = 100000);

            $comment->article()->associate(Article::find(1));
            $comment->save();
            
            for($a = 0 ; $a < 2 ; $a++){
                $scomment = new SecondComment();
                $scomment->uid = Carbon::now()->timestamp . SecondComment::count();
                $scomment->text = $faker->sentence;
                $scomment->like =  $faker->numberBetween($min = 1000, $max = 100000);
                $scomment->dislike =  $faker->numberBetween($min = 1000, $max = 100000);

                $scomment->comment()->associate($comment);
                $scomment->user()->associate(User::find($faker->numberBetween($min = 1, $max = 20)));
                $scomment->save();
            }
            
        }

        for($z = 0 ; $z < 10 ; $z++){
            $comment = new Comment();
            $comment->uid = Carbon::now()->timestamp . Comment::count();
            $comment->text = $faker->sentence;
            $comment->type = 'article';
            $comment->like =  $faker->numberBetween($min = 1000, $max = 100000);
            $comment->dislike =  $faker->numberBetween($min = 1000, $max = 100000);

            $comment->article()->associate(Article::find(2));
            $comment->save();
            for($a = 0 ; $a < 2 ; $a++){
                $scomment = new SecondComment();
                $scomment->uid = Carbon::now()->timestamp . SecondComment::count();
                $scomment->text = $faker->sentence;
                $scomment->like =  $faker->numberBetween($min = 1000, $max = 100000);
                $scomment->dislike =  $faker->numberBetween($min = 1000, $max = 100000);

                $scomment->comment()->associate($comment);
                $scomment->user()->associate(User::find($faker->numberBetween($min = 1, $max = 20)));
                $scomment->save();
            }
            
        }
        
        for($z = 0 ; $z < 10 ; $z++){
            $comment = new Comment();
            $comment->uid = Carbon::now()->timestamp . Comment::count();
            $comment->text = $faker->sentence;
            $comment->type = 'article';
            $comment->like =  $faker->numberBetween($min = 1000, $max = 100000);
            $comment->dislike =  $faker->numberBetween($min = 1000, $max = 100000);

            $comment->article()->associate(Article::find(3));
            $comment->user()->associate(User::find($faker->numberBetween($min = 1, $max = 20)));
            $comment->save();
            
            for($a = 0 ; $a < 2 ; $a++){
                $scomment = new SecondComment();
                $scomment->uid = Carbon::now()->timestamp . SecondComment::count();
                $scomment->text = $faker->sentence;
                $scomment->like =  $faker->numberBetween($min = 1000, $max = 100000);
                $scomment->dislike =  $faker->numberBetween($min = 1000, $max = 100000);

                $scomment->comment()->associate($comment);
                $scomment->user()->associate(User::find($faker->numberBetween($min = 1, $max = 20)));
                $scomment->save();
            }
            
        }

    }
}
