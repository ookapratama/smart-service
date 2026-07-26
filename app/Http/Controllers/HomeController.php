<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the landing home page.
     *
     * @return View
     */
    public function index(): View
    {
        // General site information
        $siteInfo = [
            'name' => 'BizLand',
            'hero_tagline' => 'We are team of talented designers making websites with Bootstrap & Laravel Blade',
            'email' => 'contact@example.com',
            'phone' => '+1 5589 55488 55',
            'address_line1' => 'A108 Adam Street',
            'address_line2' => 'New York, NY 535022',
            'about_short' => 'Smart Service solution providing high performance, scalable, and responsive digital services.',
            'video_url' => 'https://www.youtube.com/watch?v=Y7f98aduVJ8',
            'social_links' => [
                'twitter' => 'https://twitter.com',
                'facebook' => 'https://facebook.com',
                'instagram' => 'https://instagram.com',
                'linkedin' => 'https://linkedin.com',
            ],
        ];

        // Featured services cards
        $featuredServices = [
            [
                'icon' => 'bi-activity',
                'title' => 'Lorem Ipsum',
                'description' => 'Voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi',
                'link' => '#',
            ],
            [
                'icon' => 'bi-bounding-box-circles',
                'title' => 'Sed ut perspici',
                'description' => 'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore',
                'link' => '#',
            ],
            [
                'icon' => 'bi-calendar4-week',
                'title' => 'Magni Dolores',
                'description' => 'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia',
                'link' => '#',
            ],
            [
                'icon' => 'bi-broadcast',
                'title' => 'Nemo Enim',
                'description' => 'At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis',
                'link' => '#',
            ],
        ];

        // Statistics counters
        $stats = [
            ['icon' => 'bi-emoji-smile', 'count' => 232, 'label' => 'Happy Clients'],
            ['icon' => 'bi-journal-richtext', 'count' => 521, 'label' => 'Projects Completed'],
            ['icon' => 'bi-headset', 'count' => 1463, 'label' => 'Hours Of Support'],
            ['icon' => 'bi-people', 'count' => 25, 'label' => 'Hard Workers'],
        ];

        // Full services list
        $services = [
            [
                'icon' => 'bi-activity',
                'title' => 'Nesciunt Mete',
                'description' => 'Provident nihil minus qui consequatur non omnis maiores. Eos accusantium minus dolores iure perferendis.',
                'link' => '#',
            ],
            [
                'icon' => 'bi-broadcast',
                'title' => 'Eosle Commodi',
                'description' => 'Ut autem aut eum quas nesciunt eos ut temporibus inventore veritatis nesciunt elit.',
                'link' => '#',
            ],
            [
                'icon' => 'bi-easel',
                'title' => 'Ledo Mare',
                'description' => 'Ut occaecati cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
                'link' => '#',
            ],
            [
                'icon' => 'bi-bounding-box-circles',
                'title' => 'Asperiores Commodit',
                'description' => 'Non et temporibus minus omnis sed dolor esse consequatur. Cupiditate sed error ea.',
                'link' => '#',
            ],
            [
                'icon' => 'bi-calendar4-week',
                'title' => 'Velit Doloremque',
                'description' => 'Cumque et es et est asperiores consequuntur eos. Pretium id eos consequatur amet ea.',
                'link' => '#',
            ],
            [
                'icon' => 'bi-chat-square-text',
                'title' => 'Dolori Architecto',
                'description' => 'Hic molestias ea omnis elit. Qui unde architecto veritatis. Sit error est et id et est.',
                'link' => '#',
            ],
        ];

        // Team members
        $teams = [
            [
                'image' => 'assets/home/img/team/team-1.jpg',
                'name' => 'Walter White',
                'position' => 'Chief Executive Officer',
                'socials' => [
                    'twitter' => '#',
                    'facebook' => '#',
                    'instagram' => '#',
                    'linkedin' => '#',
                ],
            ],
            [
                'image' => 'assets/home/img/team/team-2.jpg',
                'name' => 'Sarah Jhonson',
                'position' => 'Product Manager',
                'socials' => [
                    'twitter' => '#',
                    'facebook' => '#',
                    'instagram' => '#',
                    'linkedin' => '#',
                ],
            ],
            [
                'image' => 'assets/home/img/team/team-3.jpg',
                'name' => 'William Anderson',
                'position' => 'CTO',
                'socials' => [
                    'twitter' => '#',
                    'facebook' => '#',
                    'instagram' => '#',
                    'linkedin' => '#',
                ],
            ],
            [
                'image' => 'assets/home/img/team/team-4.jpg',
                'name' => 'Amanda Jepson',
                'position' => 'Accountant',
                'socials' => [
                    'twitter' => '#',
                    'facebook' => '#',
                    'instagram' => '#',
                    'linkedin' => '#',
                ],
            ],
        ];

        return view('home.index', compact('siteInfo', 'featuredServices', 'stats', 'services', 'teams'));
    }
}
