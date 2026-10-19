<?php

namespace Database\Seeders;

use App\Models\Illustration;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IllustrationSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('DELETE FROM illustrations');

        $scenes = $this->getScenesData();

        foreach ($scenes as $sceneData) {
            Illustration::create([
                'title'       => $sceneData['title'],
                'category'    => $sceneData['category'],
                'view_box'    => '0 0 800 600',
                'description' => $sceneData['description'],
                'svg_content' => $sceneData['svg_content'],
            ]);
        }
    }

    private function getScenesData(): array
    {
        return [
            // 1. Городская кофейня
            [
                'title' => 'Городская кофейня',
                'category' => 'Город и Архитектура',
                'description' => 'Уютный фасад кофейни с бариста, столиком и фонарем',
                'svg_content' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600" width="100%" height="100%">
  <defs>
    <linearGradient id="skyGrad1" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0%" stop-color="#E0F2FE"/>
      <stop offset="100%" stop-color="#F8FAFC"/>
    </linearGradient>
  </defs>
  <rect width="800" height="600" fill="url(#skyGrad1)"/>
  <rect x="0" y="480" width="800" height="120" fill="#E2E8F0"/>
  <line x1="0" y1="480" x2="800" y2="480" stroke="#CBD5E1" stroke-width="4"/>

  <!-- Здание кофейни -->
  <g id="coffee_shop_building" data-name="Здание кофейни" data-category="Здания">
    <rect x="80" y="140" width="340" height="340" rx="12" fill="#3B82F6"/>
    <rect x="100" y="160" width="300" height="40" rx="6" fill="#1D4ED8"/>
    <text x="250" y="186" fill="#FFFFFF" font-family="Arial, sans-serif" font-weight="bold" font-size="20" text-anchor="middle" letter-spacing="3">COFFEE &amp; CO</text>
    <path d="M70,200 L430,200 L410,230 L90,230 Z" fill="#EF4444"/>
    <path d="M100,200 L130,200 L125,230 L95,230 Z" fill="#FFFFFF"/>
    <path d="M160,200 L190,200 L185,230 L155,230 Z" fill="#FFFFFF"/>
    <path d="M220,200 L250,200 L245,230 L215,230 Z" fill="#FFFFFF"/>
    <path d="M280,200 L310,200 L305,230 L275,230 Z" fill="#FFFFFF"/>
    <path d="M340,200 L370,200 L365,230 L335,230 Z" fill="#FFFFFF"/>
    <path d="M400,200 L430,200 L425,230 L395,230 Z" fill="#FFFFFF"/>
    <!-- Окно-витрина -->
    <rect x="110" y="260" width="160" height="180" rx="8" fill="#BAE6FD" stroke="#1E40AF" stroke-width="6"/>
    <line x1="190" y1="260" x2="190" y2="440" stroke="#1E40AF" stroke-width="4"/>
    <!-- Дверь -->
    <rect x="300" y="280" width="90" height="200" rx="6" fill="#1E293B"/>
    <rect x="315" y="300" width="60" height="80" rx="4" fill="#93C5FD"/>
    <circle cx="312" cy="380" r="5" fill="#FBBF24"/>
  </g>

  <!-- Бариста -->
  <g id="barista_person" data-name="Бариста" data-category="Персонажи">
    <circle cx="460" cy="320" r="22" fill="#FDE047"/>
    <path d="M445,310 Q460,295 475,310 Q460,302 445,310 Z" fill="#78350F"/>
    <rect x="442" y="342" width="36" height="70" rx="8" fill="#10B981"/>
    <path d="M446,360 L474,360 L470,412 L450,412 Z" fill="#047857"/>
    <rect x="446" y="412" width="12" height="68" fill="#1E293B"/>
    <rect x="462" y="412" width="12" height="68" fill="#1E293B"/>
    <circle cx="442" cy="480" r="8" fill="#0F172A"/>
    <circle cx="478" cy="480" r="8" fill="#0F172A"/>
  </g>

  <!-- Столик со стульями -->
  <g id="coffee_table_set" data-name="Столик со стульями" data-category="Мебель">
    <ellipse cx="620" cy="420" rx="50" ry="12" fill="#F59E0B"/>
    <rect x="616" y="420" width="8" height="60" fill="#78350F"/>
    <ellipse cx="620" cy="480" rx="30" ry="6" fill="#78350F"/>
    <!-- Левый стул -->
    <rect x="540" y="400" width="35" height="8" rx="3" fill="#D97706"/>
    <path d="M545,360 L545,400 M570,360 L570,400" stroke="#78350F" stroke-width="4"/>
    <rect x="542" y="360" width="30" height="10" rx="2" fill="#D97706"/>
    <line x1="545" y1="408" x2="540" y2="480" stroke="#78350F" stroke-width="4"/>
    <line x1="570" y1="408" x2="575" y2="480" stroke="#78350F" stroke-width="4"/>
    <!-- Правый стул -->
    <rect x="665" y="400" width="35" height="8" rx="3" fill="#D97706"/>
    <path d="M670,360 L670,400 M695,360 L695,400" stroke="#78350F" stroke-width="4"/>
    <rect x="667" y="360" width="30" height="10" rx="2" fill="#D97706"/>
    <line x1="670" y1="408" x2="665" y2="480" stroke="#78350F" stroke-width="4"/>
    <line x1="695" y1="408" x2="700" y2="480" stroke="#78350F" stroke-width="4"/>
  </g>

  <!-- Уличный фонарь -->
  <g id="street_lantern" data-name="Уличный фонарь" data-category="Декор">
    <rect x="735" y="240" width="8" height="240" fill="#1E293B"/>
    <path d="M725,240 L753,240 L748,210 L730,210 Z" fill="#FBBF24" stroke="#1E293B" stroke-width="3"/>
    <path d="M720,210 L758,210 L739,185 Z" fill="#1E293B"/>
    <rect x="725" y="475" width="28" height="10" rx="3" fill="#1E293B"/>
  </g>

  <!-- Растение в кадке -->
  <g id="potted_ficus" data-name="Фикус в кадке" data-category="Природа">
    <polygon points="490,480 500,430 530,430 540,480" fill="#B45309"/>
    <circle cx="515" cy="410" r="28" fill="#10B981"/>
    <circle cx="498" cy="385" r="22" fill="#059669"/>
    <circle cx="532" cy="385" r="22" fill="#34D399"/>
  </g>
</svg>',
                'components' => [
                    [
                        'title' => 'Здание кофейни',
                        'category' => 'Здания',
                        'view_box' => '70 140 360 340',
                        'width' => 360,
                        'height' => 340,
                        'svg_content' => '<g>
  <rect x="80" y="140" width="340" height="340" rx="12" fill="#3B82F6"/>
  <rect x="100" y="160" width="300" height="40" rx="6" fill="#1D4ED8"/>
  <text x="250" y="186" fill="#FFFFFF" font-family="Arial, sans-serif" font-weight="bold" font-size="20" text-anchor="middle" letter-spacing="3">COFFEE &amp; CO</text>
  <path d="M70,200 L430,200 L410,230 L90,230 Z" fill="#EF4444"/>
  <path d="M100,200 L130,200 L125,230 L95,230 Z" fill="#FFFFFF"/>
  <path d="M160,200 L190,200 L185,230 L155,230 Z" fill="#FFFFFF"/>
  <path d="M220,200 L250,200 L245,230 L215,230 Z" fill="#FFFFFF"/>
  <path d="M280,200 L310,200 L305,230 L275,230 Z" fill="#FFFFFF"/>
  <path d="M340,200 L370,200 L365,230 L335,230 Z" fill="#FFFFFF"/>
  <path d="M400,200 L430,200 L425,230 L395,230 Z" fill="#FFFFFF"/>
  <rect x="110" y="260" width="160" height="180" rx="8" fill="#BAE6FD" stroke="#1E40AF" stroke-width="6"/>
  <line x1="190" y1="260" x2="190" y2="440" stroke="#1E40AF" stroke-width="4"/>
  <rect x="300" y="280" width="90" height="200" rx="6" fill="#1E293B"/>
  <rect x="315" y="300" width="60" height="80" rx="4" fill="#93C5FD"/>
  <circle cx="312" cy="380" r="5" fill="#FBBF24"/>
</g>',
                        'tags' => ['город', 'кафе', 'здание'],
                    ],
                    [
                        'title' => 'Столик со стульями',
                        'category' => 'Мебель',
                        'view_box' => '530 350 180 140',
                        'width' => 180,
                        'height' => 140,
                        'svg_content' => '<g>
  <ellipse cx="620" cy="420" rx="50" ry="12" fill="#F59E0B"/>
  <rect x="616" y="420" width="8" height="60" fill="#78350F"/>
  <ellipse cx="620" cy="480" rx="30" ry="6" fill="#78350F"/>
  <rect x="540" y="400" width="35" height="8" rx="3" fill="#D97706"/>
  <path d="M545,360 L545,400 M570,360 L570,400" stroke="#78350F" stroke-width="4"/>
  <rect x="542" y="360" width="30" height="10" rx="2" fill="#D97706"/>
  <line x1="545" y1="408" x2="540" y2="480" stroke="#78350F" stroke-width="4"/>
  <line x1="570" y1="408" x2="575" y2="480" stroke="#78350F" stroke-width="4"/>
  <rect x="665" y="400" width="35" height="8" rx="3" fill="#D97706"/>
  <path d="M670,360 L670,400 M695,360 L695,400" stroke="#78350F" stroke-width="4"/>
  <rect x="667" y="360" width="30" height="10" rx="2" fill="#D97706"/>
  <line x1="670" y1="408" x2="665" y2="480" stroke="#78350F" stroke-width="4"/>
  <line x1="695" y1="408" x2="700" y2="480" stroke="#78350F" stroke-width="4"/>
</g>',
                        'tags' => ['кафе', 'мебель', 'стол', 'стулья'],
                    ],
                    [
                        'title' => 'Уличный фонарь',
                        'category' => 'Декор',
                        'view_box' => '715 180 50 310',
                        'width' => 60,
                        'height' => 310,
                        'svg_content' => '<g>
  <rect x="735" y="240" width="8" height="240" fill="#1E293B"/>
  <path d="M725,240 L753,240 L748,210 L730,210 Z" fill="#FBBF24" stroke="#1E293B" stroke-width="3"/>
  <path d="M720,210 L758,210 L739,185 Z" fill="#1E293B"/>
  <rect x="725" y="475" width="28" height="10" rx="3" fill="#1E293B"/>
</g>',
                        'tags' => ['улица', 'свет', 'фонарь', 'город'],
                    ],
                ],
            ],

            // 2. Городская пекарня
            [
                'title' => 'Городская пекарня',
                'category' => 'Город и Архитектура',
                'description' => 'Ароматная пекарня с круассанами, корзиной багетов и пекарем',
                'svg_content' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600" width="100%" height="100%">
  <rect width="800" height="600" fill="#FEF3C7"/>
  <rect x="0" y="480" width="800" height="120" fill="#E2E8F0"/>

  <!-- Здание пекарни -->
  <g id="bakery_facade" data-name="Здание пекарни" data-category="Здания">
    <rect x="120" y="160" width="380" height="320" rx="8" fill="#F97316"/>
    <rect x="140" y="180" width="340" height="50" rx="6" fill="#7C2D12"/>
    <text x="310" y="212" fill="#FDE68A" font-family="Arial, sans-serif" font-weight="bold" font-size="22" text-anchor="middle">LA BOULANGERIE</text>
    <path d="M110,230 L510,230 L490,265 L130,265 Z" fill="#EA580C"/>
    <rect x="150" y="290" width="180" height="170" rx="8" fill="#FFFBEB" stroke="#9A3412" stroke-width="5"/>
    <rect x="360" y="300" width="110" height="180" rx="6" fill="#7C2D12"/>
    <rect x="380" y="320" width="70" height="70" rx="4" fill="#FDE68A"/>
  </g>

  <!-- Корзина со свежими багетами -->
  <g id="baguette_basket" data-name="Корзина с багетами" data-category="Предметы">
    <polygon points="540,480 550,400 610,400 620,480" fill="#D97706" stroke="#92400E" stroke-width="3"/>
    <!-- Багеты -->
    <rect x="555" y="340" width="16" height="80" rx="8" fill="#F59E0B" transform="rotate(-12 555 340)"/>
    <rect x="575" y="330" width="16" height="90" rx="8" fill="#D97706"/>
    <rect x="595" y="340" width="16" height="80" rx="8" fill="#F59E0B" transform="rotate(12 595 340)"/>
  </g>

  <!-- Меловая доска меню -->
  <g id="chalk_board_menu" data-name="Меловая доска меню" data-category="Декор">
    <polygon points="660,480 675,370 735,370 750,480" fill="#78350F"/>
    <rect x="670" y="380" width="70" height="90" rx="4" fill="#1E293B"/>
    <line x1="680" y1="400" x2="730" y2="400" stroke="#FFFFFF" stroke-width="3"/>
    <line x1="680" y1="415" x2="720" y2="415" stroke="#FDE68A" stroke-width="2"/>
    <line x1="680" y1="430" x2="710" y2="430" stroke="#FDE68A" stroke-width="2"/>
  </g>
</svg>',
                'components' => [
                    [
                        'title' => 'Корзина с багетами',
                        'category' => 'Предметы',
                        'view_box' => '540 320 90 165',
                        'width' => 90,
                        'height' => 165,
                        'svg_content' => '<g>
  <polygon points="540,480 550,400 610,400 620,480" fill="#D97706" stroke="#92400E" stroke-width="3"/>
  <rect x="555" y="340" width="16" height="80" rx="8" fill="#F59E0B" transform="rotate(-12 555 340)"/>
  <rect x="575" y="330" width="16" height="90" rx="8" fill="#D97706"/>
  <rect x="595" y="340" width="16" height="80" rx="8" fill="#F59E0B" transform="rotate(12 595 340)"/>
</g>',
                        'tags' => ['еда', 'хлеб', 'пекарня'],
                    ],
                    [
                        'title' => 'Меловая доска меню',
                        'category' => 'Декор',
                        'view_box' => '655 365 100 120',
                        'width' => 100,
                        'height' => 120,
                        'svg_content' => '<g>
  <polygon points="660,480 675,370 735,370 750,480" fill="#78350F"/>
  <rect x="670" y="380" width="70" height="90" rx="4" fill="#1E293B"/>
  <line x1="680" y1="400" x2="730" y2="400" stroke="#FFFFFF" stroke-width="3"/>
  <line x1="680" y1="415" x2="720" y2="415" stroke="#FDE68A" stroke-width="2"/>
  <line x1="680" y1="430" x2="710" y2="430" stroke="#FDE68A" stroke-width="2"/>
</g>',
                        'tags' => ['меню', 'кафе', 'вывеска'],
                    ],
                ],
            ],

            // 3. Офис разработчика
            [
                'title' => 'Рабочее место разработчика',
                'category' => 'Рабочее пространство',
                'description' => 'Минималистичный стол, компьютер, лампа и кактус',
                'svg_content' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600" width="100%" height="100%">
  <rect width="800" height="600" fill="#F1F5F9"/>
  <rect x="0" y="500" width="800" height="100" fill="#CBD5E1"/>

  <!-- Рабочий стол -->
  <g id="work_desk" data-name="Рабочий стол" data-category="Мебель">
    <rect x="150" y="360" width="500" height="24" rx="4" fill="#38BDF8"/>
    <rect x="180" y="384" width="16" height="130" fill="#0284C7"/>
    <rect x="604" y="384" width="16" height="130" fill="#0284C7"/>
  </g>

  <!-- Монитор iMac -->
  <g id="computer_monitor" data-name="Компьютерный монитор" data-category="Предметы">
    <rect x="310" y="190" width="200" height="130" rx="8" fill="#1E293B"/>
    <rect x="318" y="198" width="184" height="114" rx="4" fill="#3B82F6"/>
    <polygon points="340,240 380,240 370,280 330,280" fill="#FFFFFF" opacity="0.3"/>
    <!-- Подставка -->
    <rect x="395" y="320" width="30" height="40" fill="#64748B"/>
    <rect x="375" y="356" width="70" height="6" rx="3" fill="#475569"/>
    <!-- Клавиатура -->
    <rect x="340" y="368" width="120" height="10" rx="3" fill="#E2E8F0"/>
  </g>

  <!-- Настольная лампа -->
  <g id="desk_lamp" data-name="Настольная лампа" data-category="Предметы">
    <ellipse cx="230" cy="365" rx="24" ry="6" fill="#F59E0B"/>
    <path d="M230,362 L220,290 L260,240" stroke="#D97706" stroke-width="6" fill="none" stroke-linecap="round"/>
    <path d="M245,225 L285,255 L255,275 Z" fill="#F59E0B"/>
  </g>

  <!-- Растение Кактус -->
  <g id="desk_cactus" data-name="Кактус на столе" data-category="Природа">
    <polygon points="560,366 565,330 595,330 600,366" fill="#EA580C"/>
    <rect x="572" y="280" width="16" height="50" rx="8" fill="#10B981"/>
    <path d="M565,300 L572,300 M588,295 L595,295" stroke="#10B981" stroke-width="6" stroke-linecap="round"/>
  </g>
</svg>',
                'components' => [
                    [
                        'title' => 'Компьютерный монитор',
                        'category' => 'Предметы',
                        'view_box' => '305 185 210 185',
                        'width' => 200,
                        'height' => 175,
                        'svg_content' => '<g>
  <rect x="310" y="190" width="200" height="130" rx="8" fill="#1E293B"/>
  <rect x="318" y="198" width="184" height="114" rx="4" fill="#3B82F6"/>
  <polygon points="340,240 380,240 370,280 330,280" fill="#FFFFFF" opacity="0.3"/>
  <rect x="395" y="320" width="30" height="40" fill="#64748B"/>
  <rect x="375" y="356" width="70" height="6" rx="3" fill="#475569"/>
  <rect x="340" y="368" width="120" height="10" rx="3" fill="#E2E8F0"/>
</g>',
                        'tags' => ['компьютер', 'офис', 'гаджет'],
                    ],
                    [
                        'title' => 'Настольная лампа',
                        'category' => 'Предметы',
                        'view_box' => '200 220 90 150',
                        'width' => 90,
                        'height' => 150,
                        'svg_content' => '<g>
  <ellipse cx="230" cy="365" rx="24" ry="6" fill="#F59E0B"/>
  <path d="M230,362 L220,290 L260,240" stroke="#D97706" stroke-width="6" fill="none" stroke-linecap="round"/>
  <path d="M245,225 L285,255 L255,275 Z" fill="#F59E0B"/>
</g>',
                        'tags' => ['лампа', 'свет', 'стол'],
                    ],
                    [
                        'title' => 'Кактус на столе',
                        'category' => 'Природа',
                        'view_box' => '555 275 55 95',
                        'width' => 60,
                        'height' => 95,
                        'svg_content' => '<g>
  <polygon points="560,366 565,330 595,330 600,366" fill="#EA580C"/>
  <rect x="572" y="280" width="16" height="50" rx="8" fill="#10B981"/>
  <path d="M565,300 L572,300 M588,295 L595,295" stroke="#10B981" stroke-width="6" stroke-linecap="round"/>
</g>',
                        'tags' => ['растение', 'кактус', 'природа'],
                    ],
                ],
            ],

            // 4. Городской парк
            [
                'title' => 'Городской парк',
                'category' => 'Природа и Отдых',
                'description' => 'Зеленая зона отдыха с дубом, скамейкой и фонарем',
                'svg_content' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600" width="100%" height="100%">
  <rect width="800" height="600" fill="#ECFDF5"/>
  <rect x="0" y="460" width="800" height="140" fill="#10B981"/>

  <!-- Ветвистый дуб -->
  <g id="park_tree" data-name="Ветвистый дуб" data-category="Природа">
    <rect x="210" y="260" width="40" height="210" fill="#78350F" rx="6"/>
    <!-- Крона дерева -->
    <circle cx="230" cy="210" r="90" fill="#059669"/>
    <circle cx="170" cy="240" r="60" fill="#10B981"/>
    <circle cx="290" cy="230" r="65" fill="#34D399"/>
    <circle cx="230" cy="140" r="70" fill="#047857"/>
  </g>

  <!-- Парковая скамейка -->
  <g id="park_bench" data-name="Парковая скамейка" data-category="Мебель">
    <rect x="460" y="390" width="220" height="14" rx="4" fill="#D97706"/>
    <rect x="460" y="414" width="220" height="14" rx="4" fill="#D97706"/>
    <rect x="460" y="445" width="220" height="16" rx="4" fill="#B45309"/>
    <!-- Ножки скамейки -->
    <path d="M480,440 L470,480 M510,440 L515,480" stroke="#1E293B" stroke-width="6"/>
    <path d="M640,440 L635,480 M670,440 L675,480" stroke="#1E293B" stroke-width="6"/>
    <line x1="480" y1="390" x2="480" y2="450" stroke="#1E293B" stroke-width="6"/>
    <line x1="660" y1="390" x2="660" y2="450" stroke="#1E293B" stroke-width="6"/>
  </g>

  <!-- Цветущий куст -->
  <g id="flowering_bush" data-name="Цветущий куст" data-category="Природа">
    <ellipse cx="380" cy="460" rx="45" ry="30" fill="#047857"/>
    <circle cx="365" cy="450" r="6" fill="#F43F5E"/>
    <circle cx="395" cy="445" r="6" fill="#FB7185"/>
    <circle cx="380" cy="465" r="6" fill="#F43F5E"/>
  </g>
</svg>',
                'components' => [
                    [
                        'title' => 'Ветвистый дуб',
                        'category' => 'Природа',
                        'view_box' => '100 65 260 410',
                        'width' => 260,
                        'height' => 410,
                        'svg_content' => '<g>
  <rect x="210" y="260" width="40" height="210" fill="#78350F" rx="6"/>
  <circle cx="230" cy="210" r="90" fill="#059669"/>
  <circle cx="170" cy="240" r="60" fill="#10B981"/>
  <circle cx="290" cy="230" r="65" fill="#34D399"/>
  <circle cx="230" cy="140" r="70" fill="#047857"/>
</g>',
                        'tags' => ['дерево', 'растение', 'парк', 'природа'],
                    ],
                    [
                        'title' => 'Парковая скамейка',
                        'category' => 'Мебель',
                        'view_box' => '450 380 240 110',
                        'width' => 240,
                        'height' => 110,
                        'svg_content' => '<g>
  <rect x="460" y="390" width="220" height="14" rx="4" fill="#D97706"/>
  <rect x="460" y="414" width="220" height="14" rx="4" fill="#D97706"/>
  <rect x="460" y="445" width="220" height="16" rx="4" fill="#B45309"/>
  <path d="M480,440 L470,480 M510,440 L515,480" stroke="#1E293B" stroke-width="6"/>
  <path d="M640,440 L635,480 M670,440 L675,480" stroke="#1E293B" stroke-width="6"/>
  <line x1="480" y1="390" x2="480" y2="450" stroke="#1E293B" stroke-width="6"/>
  <line x1="660" y1="390" x2="660" y2="450" stroke="#1E293B" stroke-width="6"/>
</g>',
                        'tags' => ['скамейка', 'мебель', 'парк', 'отдых'],
                    ],
                ],
            ],

            // 5. Велопрогулка
            [
                'title' => 'Велопрогулка',
                'category' => 'Транспорт и Движение',
                'description' => 'Персонаж на современном городском велосипеде',
                'svg_content' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600" width="100%" height="100%">
  <rect width="800" height="600" fill="#F0FDF4"/>
  <rect x="0" y="470" width="800" height="130" fill="#334155"/>
  <line x1="0" y1="535" x2="800" y2="535" stroke="#FFFFFF" stroke-dasharray="20,15" stroke-width="4"/>

  <!-- Велосипедист на велосипеде -->
  <g id="cyclist_riding" data-name="Велосипедист" data-category="Персонажи">
    <!-- Велосипед -->
    <circle cx="260" cy="430" r="50" fill="none" stroke="#0F172A" stroke-width="8"/>
    <circle cx="440" cy="430" r="50" fill="none" stroke="#0F172A" stroke-width="8"/>
    <!-- Рама велосипеда -->
    <path d="M260,430 L340,430 L410,360 L320,360 Z" fill="none" stroke="#EF4444" stroke-width="8" stroke-linejoin="round"/>
    <path d="M340,430 L340,335 M440,430 L415,340" stroke="#EF4444" stroke-width="8"/>
    <path d="M400,340 L435,340" stroke="#0F172A" stroke-width="8" stroke-linecap="round"/>
    <rect x="325" y="330" width="30" height="8" rx="4" fill="#0F172A"/>

    <!-- Персонаж -->
    <circle cx="360" cy="230" r="22" fill="#FED7AA"/>
    <!-- Шлем -->
    <path d="M340,230 Q360,200 385,225 Z" fill="#3B82F6"/>
    <!-- Тело -->
    <path d="M355,252 L345,340 L385,340 Z" fill="#6366F1"/>
    <!-- Нога на педали -->
    <path d="M345,335 L370,390 L340,430" stroke="#1E293B" stroke-width="10" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
  </g>

  <!-- Дорожный знак Велодорожка -->
  <g id="bike_lane_sign" data-name="Знак Велосипедная дорожка" data-category="Декор">
    <rect x="620" y="240" width="8" height="230" fill="#64748B"/>
    <circle cx="624" cy="240" r="38" fill="#2563EB" stroke="#FFFFFF" stroke-width="4"/>
    <path d="M605,250 A10,10 0 1,0 620,250 A10,10 0 1,0 605,250" fill="none" stroke="#FFFFFF" stroke-width="3"/>
    <path d="M635,250 A10,10 0 1,0 650,250 A10,10 0 1,0 635,250" fill="none" stroke="#FFFFFF" stroke-width="3"/>
    <line x1="612" y1="250" x2="630" y2="235" stroke="#FFFFFF" stroke-width="3"/>
  </g>
</svg>',
                'components' => [
                    [
                        'title' => 'Велосипедист',
                        'category' => 'Персонажи',
                        'view_box' => '200 200 290 285',
                        'width' => 290,
                        'height' => 285,
                        'svg_content' => '<g>
  <circle cx="260" cy="430" r="50" fill="none" stroke="#0F172A" stroke-width="8"/>
  <circle cx="440" cy="430" r="50" fill="none" stroke="#0F172A" stroke-width="8"/>
  <path d="M260,430 L340,430 L410,360 L320,360 Z" fill="none" stroke="#EF4444" stroke-width="8" stroke-linejoin="round"/>
  <path d="M340,430 L340,335 M440,430 L415,340" stroke="#EF4444" stroke-width="8"/>
  <path d="M400,340 L435,340" stroke="#0F172A" stroke-width="8" stroke-linecap="round"/>
  <rect x="325" y="330" width="30" height="8" rx="4" fill="#0F172A"/>
  <circle cx="360" cy="230" r="22" fill="#FED7AA"/>
  <path d="M340,230 Q360,200 385,225 Z" fill="#3B82F6"/>
  <path d="M355,252 L345,340 L385,340 Z" fill="#6366F1"/>
  <path d="M345,335 L370,390 L340,430" stroke="#1E293B" stroke-width="10" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
</g>',
                        'tags' => ['велосипед', 'спорт', 'персонаж', 'транспорт'],
                    ],
                ],
            ],

            // 6. Уютный таунхаус
            [
                'title' => 'Городской таунхаус',
                'category' => 'Город и Архитектура',
                'description' => 'Двухэтажный дом с мансардными окнами и палисадником',
                'svg_content' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600" width="100%" height="100%">
  <rect width="800" height="600" fill="#E0F2FE"/>
  <rect x="0" y="470" width="800" height="130" fill="#10B981"/>

  <!-- Таунхаус -->
  <g id="townhouse_house" data-name="Таунхаус" data-category="Здания">
    <rect x="220" y="200" width="360" height="270" fill="#F8FAFC" stroke="#0F172A" stroke-width="4"/>
    <!-- Крыша -->
    <polygon points="190,200 400,80 610,200" fill="#EF4444" stroke="#0F172A" stroke-width="4"/>
    <!-- Окна 2 этажа -->
    <rect x="260" y="230" width="70" height="80" rx="4" fill="#38BDF8" stroke="#0F172A" stroke-width="4"/>
    <rect x="470" y="230" width="70" height="80" rx="4" fill="#38BDF8" stroke="#0F172A" stroke-width="4"/>
    <!-- Дверь -->
    <rect x="360" y="340" width="80" height="130" rx="6" fill="#1E293B"/>
    <circle cx="425" cy="410" r="5" fill="#F59E0B"/>
    <!-- Труба -->
    <rect x="490" y="100" width="36" height="60" fill="#991B1B"/>
  </g>

  <!-- Белый заборчик -->
  <g id="white_fence" data-name="Белый заборчик" data-category="Декор">
    <path d="M120,440 L130,420 L140,440 L140,470 L120,470 Z" fill="#FFFFFF" stroke="#CBD5E1" stroke-width="2"/>
    <path d="M145,440 L155,420 L165,440 L165,470 L145,470 Z" fill="#FFFFFF" stroke="#CBD5E1" stroke-width="2"/>
    <path d="M170,440 L180,420 L190,440 L190,470 L170,470 Z" fill="#FFFFFF" stroke="#CBD5E1" stroke-width="2"/>
    <line x1="110" y1="450" x2="200" y2="450" stroke="#CBD5E1" stroke-width="4"/>
  </g>
</svg>',
                'components' => [
                    [
                        'title' => 'Таунхаус',
                        'category' => 'Здания',
                        'view_box' => '180 75 440 400',
                        'width' => 440,
                        'height' => 400,
                        'svg_content' => '<g>
  <rect x="220" y="200" width="360" height="270" fill="#F8FAFC" stroke="#0F172A" stroke-width="4"/>
  <polygon points="190,200 400,80 610,200" fill="#EF4444" stroke="#0F172A" stroke-width="4"/>
  <rect x="260" y="230" width="70" height="80" rx="4" fill="#38BDF8" stroke="#0F172A" stroke-width="4"/>
  <rect x="470" y="230" width="70" height="80" rx="4" fill="#38BDF8" stroke="#0F172A" stroke-width="4"/>
  <rect x="360" y="340" width="80" height="130" rx="6" fill="#1E293B"/>
  <circle cx="425" cy="410" r="5" fill="#F59E0B"/>
  <rect x="490" y="100" width="36" height="60" fill="#991B1B"/>
</g>',
                        'tags' => ['дом', 'архитектура', 'здание'],
                    ],
                ],
            ],

            // 7. Доставка еды на скутере
            [
                'title' => 'Курьер на скутере',
                'category' => 'Транспорт и Движение',
                'description' => 'Курьер доставки еды на стильном мотороллере',
                'svg_content' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600" width="100%" height="100%">
  <rect width="800" height="600" fill="#FEF2F2"/>
  <rect x="0" y="470" width="800" height="130" fill="#475569"/>

  <!-- Скутер доставки -->
  <g id="delivery_scooter" data-name="Скутер курьера" data-category="Транспорт">
    <!-- Колеса -->
    <circle cx="290" cy="450" r="36" fill="#1E293B" stroke="#94A3B8" stroke-width="8"/>
    <circle cx="470" cy="450" r="36" fill="#1E293B" stroke="#94A3B8" stroke-width="8"/>
    <!-- Кузов скутера -->
    <path d="M290,450 L380,450 L420,380 L460,380 L470,450" fill="#F59E0B" stroke="#B45309" stroke-width="4"/>
    <rect x="250" y="330" width="60" height="70" rx="6" fill="#EF4444"/>
    <text x="280" y="370" fill="#FFFFFF" font-weight="bold" font-size="14" text-anchor="middle">FOOD</text>
    <path d="M430,380 L450,310 L430,305" stroke="#1E293B" stroke-width="6" fill="none"/>
  </g>

  <!-- Светофор -->
  <g id="traffic_light" data-name="Светофор" data-category="Декор">
    <rect x="620" y="140" width="40" height="110" rx="10" fill="#1E293B"/>
    <circle cx="640" cy="160" r="11" fill="#EF4444"/>
    <circle cx="640" cy="195" r="11" fill="#F59E0B"/>
    <circle cx="640" cy="230" r="11" fill="#10B981"/>
    <rect x="636" y="250" width="8" height="220" fill="#64748B"/>
  </g>
</svg>',
                'components' => [
                    [
                        'title' => 'Скутер курьера',
                        'category' => 'Транспорт',
                        'view_box' => '240 300 280 190',
                        'width' => 280,
                        'height' => 190,
                        'svg_content' => '<g>
  <circle cx="290" cy="450" r="36" fill="#1E293B" stroke="#94A3B8" stroke-width="8"/>
  <circle cx="470" cy="450" r="36" fill="#1E293B" stroke="#94A3B8" stroke-width="8"/>
  <path d="M290,450 L380,450 L420,380 L460,380 L470,450" fill="#F59E0B" stroke="#B45309" stroke-width="4"/>
  <rect x="250" y="330" width="60" height="70" rx="6" fill="#EF4444"/>
  <text x="280" y="370" fill="#FFFFFF" font-weight="bold" font-size="14" text-anchor="middle">FOOD</text>
  <path d="M430,380 L450,310 L430,305" stroke="#1E293B" stroke-width="6" fill="none"/>
</g>',
                        'tags' => ['скутер', 'мопед', 'доставка', 'транспорт'],
                    ],
                    [
                        'title' => 'Светофор',
                        'category' => 'Декор',
                        'view_box' => '610 135 60 340',
                        'width' => 60,
                        'height' => 340,
                        'svg_content' => '<g>
  <rect x="620" y="140" width="40" height="110" rx="10" fill="#1E293B"/>
  <circle cx="640" cy="160" r="11" fill="#EF4444"/>
  <circle cx="640" cy="195" r="11" fill="#F59E0B"/>
  <circle cx="640" cy="230" r="11" fill="#10B981"/>
  <rect x="636" y="250" width="8" height="220" fill="#64748B"/>
</g>',
                        'tags' => ['светофор', 'город', 'дорога'],
                    ],
                ],
            ],

            // 8. Книжный уголок
            [
                'title' => 'Книжная гостиная',
                'category' => 'Интерьер и Уют',
                'description' => 'Уютное кресло с пледом, книжный шкаф и спящий котик',
                'svg_content' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600" width="100%" height="100%">
  <rect width="800" height="600" fill="#FAF5FF"/>
  <rect x="0" y="480" width="800" height="120" fill="#E9D5FF"/>

  <!-- Книжный шкаф -->
  <g id="bookshelf" data-name="Книжный шкаф" data-category="Мебель">
    <rect x="120" y="140" width="220" height="340" fill="#78350F" rx="6"/>
    <line x1="120" y1="220" x2="340" y2="220" stroke="#B45309" stroke-width="8"/>
    <line x1="120" y1="300" x2="340" y2="300" stroke="#B45309" stroke-width="8"/>
    <line x1="120" y1="380" x2="340" y2="380" stroke="#B45309" stroke-width="8"/>
    <!-- Книги на полках -->
    <rect x="140" y="160" width="20" height="55" rx="2" fill="#EF4444"/>
    <rect x="165" y="150" width="18" height="65" rx="2" fill="#3B82F6"/>
    <rect x="188" y="165" width="22" height="50" rx="2" fill="#10B981"/>
    <rect x="215" y="155" width="24" height="60" rx="2" fill="#F59E0B"/>
  </g>

  <!-- Мягкое кресло -->
  <g id="cozy_armchair" data-name="Мягкое кресло" data-category="Мебель">
    <path d="M460,320 Q460,260 520,260 Q580,260 580,320 L580,420 L460,420 Z" fill="#8B5CF6"/>
    <rect x="430" y="340" width="40" height="70" rx="10" fill="#7C3AED"/>
    <rect x="570" y="340" width="40" height="70" rx="10" fill="#7C3AED"/>
    <rect x="470" y="390" width="100" height="35" rx="6" fill="#6D28D9"/>
    <!-- Ножки -->
    <line x1="470" y1="425" x2="455" y2="475" stroke="#78350F" stroke-width="6"/>
    <line x1="570" y1="425" x2="585" y2="475" stroke="#78350F" stroke-width="6"/>
  </g>

  <!-- Спящий котик -->
  <g id="sleeping_cat" data-name="Спящий котик" data-category="Персонажи">
    <ellipse cx="520" cy="385" rx="26" ry="18" fill="#F97316"/>
    <circle cx="500" cy="378" r="14" fill="#EA580C"/>
    <polygon points="492,368 498,358 504,368" fill="#EA580C"/>
    <polygon points="506,368 512,358 518,368" fill="#EA580C"/>
    <path d="M535,395 Q550,385 540,375" stroke="#C2410C" stroke-width="4" fill="none"/>
  </g>
</svg>',
                'components' => [
                    [
                        'title' => 'Мягкое кресло',
                        'category' => 'Мебель',
                        'view_box' => '420 255 200 225',
                        'width' => 200,
                        'height' => 225,
                        'svg_content' => '<g>
  <path d="M460,320 Q460,260 520,260 Q580,260 580,320 L580,420 L460,420 Z" fill="#8B5CF6"/>
  <rect x="430" y="340" width="40" height="70" rx="10" fill="#7C3AED"/>
  <rect x="570" y="340" width="40" height="70" rx="10" fill="#7C3AED"/>
  <rect x="470" y="390" width="100" height="35" rx="6" fill="#6D28D9"/>
  <line x1="470" y1="425" x2="455" y2="475" stroke="#78350F" stroke-width="6"/>
  <line x1="570" y1="425" x2="585" y2="475" stroke="#78350F" stroke-width="6"/>
</g>',
                        'tags' => ['кресло', 'мебель', 'интерьер'],
                    ],
                    [
                        'title' => 'Спящий котик',
                        'category' => 'Персонажи',
                        'view_box' => '485 355 70 50',
                        'width' => 80,
                        'height' => 60,
                        'svg_content' => '<g>
  <ellipse cx="520" cy="385" rx="26" ry="18" fill="#F97316"/>
  <circle cx="500" cy="378" r="14" fill="#EA580C"/>
  <polygon points="492,368 498,358 504,368" fill="#EA580C"/>
  <polygon points="506,368 512,358 518,368" fill="#EA580C"/>
  <path d="M535,395 Q550,385 540,375" stroke="#C2410C" stroke-width="4" fill="none"/>
</g>',
                        'tags' => ['кот', 'животные', 'питомцы'],
                    ],
                ],
            ],

            // 9. Зеленая терраса
            [
                'title' => 'Сад на крыше',
                'category' => 'Природа и Отдых',
                'description' => 'Шезлонг, зонтик и зеленые растения в кашпо',
                'svg_content' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600" width="100%" height="100%">
  <rect width="800" height="600" fill="#E0F2FE"/>
  <rect x="0" y="470" width="800" height="130" fill="#F1F5F9"/>

  <!-- Зонт от солнца -->
  <g id="sun_umbrella" data-name="Пляжный зонт" data-category="Предметы">
    <path d="M220,240 Q320,130 420,240 Z" fill="#EF4444"/>
    <path d="M220,240 Q270,180 320,240 Z" fill="#FFFFFF"/>
    <path d="M320,240 Q370,180 420,240 Z" fill="#FFFFFF"/>
    <line x1="320" y1="240" x2="320" y2="470" stroke="#64748B" stroke-width="8"/>
  </g>

  <!-- Шезлонг -->
  <g id="deck_chair" data-name="Шезлонг" data-category="Мебель">
    <path d="M380,470 L480,410 L560,450 L580,470" stroke="#0284C7" stroke-width="12" stroke-linecap="round" fill="none"/>
    <line x1="430" y1="440" x2="410" y2="470" stroke="#78350F" stroke-width="8"/>
    <line x1="530" y1="435" x2="540" y2="470" stroke="#78350F" stroke-width="8"/>
  </g>

  <!-- Кашпо с монстерой -->
  <g id="potted_monstera" data-name="Кашпо с монстерой" data-category="Природа">
    <polygon points="620,470 630,410 670,410 680,470" fill="#D97706"/>
    <circle cx="650" cy="380" r="30" fill="#059669"/>
    <circle cx="630" cy="360" r="24" fill="#10B981"/>
    <circle cx="670" cy="360" r="24" fill="#34D399"/>
  </g>
</svg>',
                'components' => [
                    [
                        'title' => 'Пляжный зонт',
                        'category' => 'Предметы',
                        'view_box' => '210 140 220 335',
                        'width' => 220,
                        'height' => 335,
                        'svg_content' => '<g>
  <path d="M220,240 Q320,130 420,240 Z" fill="#EF4444"/>
  <path d="M220,240 Q270,180 320,240 Z" fill="#FFFFFF"/>
  <path d="M320,240 Q370,180 420,240 Z" fill="#FFFFFF"/>
  <line x1="320" y1="240" x2="320" y2="470" stroke="#64748B" stroke-width="8"/>
</g>',
                        'tags' => ['зонт', 'лето', 'отдых'],
                    ],
                ],
            ],

            // 10. Автобусная остановка
            [
                'title' => 'Автобусная остановка',
                'category' => 'Город и Архитектура',
                'description' => 'Современный автобус и стеклянный остановочный павильон',
                'svg_content' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600" width="100%" height="100%">
  <rect width="800" height="600" fill="#F8FAFC"/>
  <rect x="0" y="470" width="800" height="130" fill="#334155"/>

  <!-- Городской автобус -->
  <g id="city_bus" data-name="Городской автобус" data-category="Транспорт">
    <rect x="100" y="240" width="460" height="210" rx="20" fill="#3B82F6"/>
    <!-- Окна автобуса -->
    <rect x="130" y="270" width="60" height="65" rx="6" fill="#93C5FD"/>
    <rect x="210" y="270" width="75" height="65" rx="6" fill="#93C5FD"/>
    <rect x="305" y="270" width="75" height="65" rx="6" fill="#93C5FD"/>
    <rect x="400" y="270" width="75" height="65" rx="6" fill="#93C5FD"/>
    <!-- Колеса -->
    <circle cx="190" cy="450" r="40" fill="#0F172A" stroke="#94A3B8" stroke-width="10"/>
    <circle cx="450" cy="450" r="40" fill="#0F172A" stroke="#94A3B8" stroke-width="10"/>
    <!-- Фары -->
    <rect x="545" y="380" width="15" height="30" rx="4" fill="#FBBF24"/>
  </g>

  <!-- Знак остановки -->
  <g id="bus_stop_sign" data-name="Знак остановки" data-category="Декор">
    <rect x="640" y="250" width="8" height="220" fill="#64748B"/>
    <rect x="620" y="210" width="48" height="50" rx="6" fill="#2563EB"/>
    <rect x="630" y="222" width="28" height="26" rx="4" fill="#FFFFFF"/>
  </g>
</svg>',
                'components' => [
                    [
                        'title' => 'Городской автобус',
                        'category' => 'Транспорт',
                        'view_box' => '90 230 480 260',
                        'width' => 480,
                        'height' => 260,
                        'svg_content' => '<g>
  <rect x="100" y="240" width="460" height="210" rx="20" fill="#3B82F6"/>
  <rect x="130" y="270" width="60" height="65" rx="6" fill="#93C5FD"/>
  <rect x="210" y="270" width="75" height="65" rx="6" fill="#93C5FD"/>
  <rect x="305" y="270" width="75" height="65" rx="6" fill="#93C5FD"/>
  <rect x="400" y="270" width="75" height="65" rx="6" fill="#93C5FD"/>
  <circle cx="190" cy="450" r="40" fill="#0F172A" stroke="#94A3B8" stroke-width="10"/>
  <circle cx="450" cy="450" r="40" fill="#0F172A" stroke="#94A3B8" stroke-width="10"/>
  <rect x="545" y="380" width="15" height="30" rx="4" fill="#FBBF24"/>
</g>',
                        'tags' => ['автобус', 'транспорт', 'город'],
                    ],
                ],
            ],

            // 11. Мастерская художника
            [
                'title' => 'Студия живописи',
                'category' => 'Искусство и Хобби',
                'description' => 'Мольберт с холстом, палитра с красками и кисти',
                'svg_content' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600" width="100%" height="100%">
  <rect width="800" height="600" fill="#FFFBEB"/>
  <rect x="0" y="490" width="800" height="110" fill="#E2E8F0"/>

  <!-- Мольберт -->
  <g id="art_easel" data-name="Мольберт с картиной" data-category="Предметы">
    <!-- Ножки мольберта -->
    <line x1="330" y1="180" x2="260" y2="490" stroke="#78350F" stroke-width="8"/>
    <line x1="330" y1="180" x2="400" y2="490" stroke="#78350F" stroke-width="8"/>
    <line x1="330" y1="160" x2="330" y2="490" stroke="#92400E" stroke-width="8"/>
    <!-- Холст -->
    <rect x="250" y="220" width="160" height="130" rx="4" fill="#FFFFFF" stroke="#D97706" stroke-width="6"/>
    <!-- Рисунок на холсте -->
    <circle cx="330" cy="270" r="26" fill="#F59E0B"/>
    <polygon points="260,330 310,280 350,330" fill="#10B981"/>
    <polygon points="320,330 360,290 400,330" fill="#059669"/>
    <!-- Полочка под холстом -->
    <rect x="230" y="345" width="200" height="12" rx="4" fill="#78350F"/>
  </g>

  <!-- Палитра художника -->
  <g id="paint_palette" data-name="Палитра художника" data-category="Предметы">
    <ellipse cx="530" cy="430" rx="60" ry="40" fill="#FDE68A" stroke="#B45309" stroke-width="4"/>
    <circle cx="560" cy="445" r="10" fill="#FFFBEB"/>
    <circle cx="500" cy="420" r="8" fill="#EF4444"/>
    <circle cx="525" cy="405" r="8" fill="#3B82F6"/>
    <circle cx="550" cy="415" r="8" fill="#10B981"/>
    <circle cx="510" cy="445" r="8" fill="#8B5CF6"/>
  </g>
</svg>',
                'components' => [
                    [
                        'title' => 'Мольберт с картиной',
                        'category' => 'Предметы',
                        'view_box' => '220 155 220 340',
                        'width' => 220,
                        'height' => 340,
                        'svg_content' => '<g>
  <line x1="330" y1="180" x2="260" y2="490" stroke="#78350F" stroke-width="8"/>
  <line x1="330" y1="180" x2="400" y2="490" stroke="#78350F" stroke-width="8"/>
  <line x1="330" y1="160" x2="330" y2="490" stroke="#92400E" stroke-width="8"/>
  <rect x="250" y="220" width="160" height="130" rx="4" fill="#FFFFFF" stroke="#D97706" stroke-width="6"/>
  <circle cx="330" cy="270" r="26" fill="#F59E0B"/>
  <polygon points="260,330 310,280 350,330" fill="#10B981"/>
  <polygon points="320,330 360,290 400,330" fill="#059669"/>
  <rect x="230" y="345" width="200" height="12" rx="4" fill="#78350F"/>
</g>',
                        'tags' => ['мольберт', 'живопись', 'искусство'],
                    ],
                    [
                        'title' => 'Палитра художника',
                        'category' => 'Предметы',
                        'view_box' => '460 380 140 100',
                        'width' => 140,
                        'height' => 100,
                        'svg_content' => '<g>
  <ellipse cx="530" cy="430" rx="60" ry="40" fill="#FDE68A" stroke="#B45309" stroke-width="4"/>
  <circle cx="560" cy="445" r="10" fill="#FFFBEB"/>
  <circle cx="500" cy="420" r="8" fill="#EF4444"/>
  <circle cx="525" cy="405" r="8" fill="#3B82F6"/>
  <circle cx="550" cy="415" r="8" fill="#10B981"/>
  <circle cx="510" cy="445" r="8" fill="#8B5CF6"/>
</g>',
                        'tags' => ['краски', 'палитра', 'творчество'],
                    ],
                ],
            ],

            // 12. Спортивная площадка
            [
                'title' => 'Спортивная зона',
                'category' => 'Спорт и Здоровье',
                'description' => 'Турники, гири, гимнастический коврик и гантели',
                'svg_content' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600" width="100%" height="100%">
  <rect width="800" height="600" fill="#F0FDF4"/>
  <rect x="0" y="480" width="800" height="120" fill="#DCFCE7"/>

  <!-- Турник -->
  <g id="pullup_bar" data-name="Воркаут турник" data-category="Предметы">
    <line x1="200" y1="200" x2="200" y2="480" stroke="#0F172A" stroke-width="10"/>
    <line x1="380" y1="200" x2="380" y2="480" stroke="#0F172A" stroke-width="10"/>
    <line x1="180" y1="200" x2="400" y2="200" stroke="#EF4444" stroke-width="12" stroke-linecap="round"/>
  </g>

  <!-- Набор гантелей -->
  <g id="dumbbells_set" data-name="Гантели" data-category="Предметы">
    <line x1="480" y1="460" x2="550" y2="460" stroke="#94A3B8" stroke-width="8"/>
    <circle cx="480" cy="460" r="16" fill="#1E293B"/>
    <circle cx="550" cy="460" r="16" fill="#1E293B"/>
  </g>

  <!-- Спортивная гиря -->
  <g id="kettlebell" data-name="Спортивная гиря" data-category="Предметы">
    <circle cx="620" cy="450" r="26" fill="#3B82F6"/>
    <path d="M605,425 Q620,400 635,425" stroke="#1E40AF" stroke-width="8" fill="none"/>
  </g>
</svg>',
                'components' => [
                    [
                        'title' => 'Воркаут турник',
                        'category' => 'Предметы',
                        'view_box' => '170 190 240 295',
                        'width' => 240,
                        'height' => 295,
                        'svg_content' => '<g>
  <line x1="200" y1="200" x2="200" y2="480" stroke="#0F172A" stroke-width="10"/>
  <line x1="380" y1="200" x2="380" y2="480" stroke="#0F172A" stroke-width="10"/>
  <line x1="180" y1="200" x2="400" y2="200" stroke="#EF4444" stroke-width="12" stroke-linecap="round"/>
</g>',
                        'tags' => ['спорт', 'турник', 'воркаут'],
                    ],
                ],
            ],

            // 13. Скейт-парк
            [
                'title' => 'Скейт-парк',
                'category' => 'Спорт и Здоровье',
                'description' => 'Рампа, скейтборд и граффити-бумбокс',
                'svg_content' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600" width="100%" height="100%">
  <rect width="800" height="600" fill="#FEF3C7"/>
  <rect x="0" y="470" width="800" height="130" fill="#E2E8F0"/>

  <!-- Скейтборд -->
  <g id="skateboard_deck" data-name="Скейтборд" data-category="Предметы">
    <path d="M250,420 Q320,430 390,420" stroke="#8B5CF6" stroke-width="14" stroke-linecap="round" fill="none"/>
    <circle cx="270" cy="445" r="12" fill="#F59E0B"/>
    <circle cx="370" cy="445" r="12" fill="#F59E0B"/>
  </g>

  <!-- Бумбокс магнитола -->
  <g id="boombox_radio" data-name="Бумбокс" data-category="Предметы">
    <rect x="470" y="380" width="160" height="90" rx="10" fill="#1E293B"/>
    <circle cx="510" cy="425" r="26" fill="#3B82F6" stroke="#93C5FD" stroke-width="4"/>
    <circle cx="590" cy="425" r="26" fill="#3B82F6" stroke="#93C5FD" stroke-width="4"/>
    <rect x="535" y="390" width="30" height="20" rx="3" fill="#64748B"/>
    <!-- Ручка бумбокса -->
    <path d="M510,380 L510,360 L590,360 L590,380" stroke="#64748B" stroke-width="6" fill="none"/>
  </g>
</svg>',
                'components' => [
                    [
                        'title' => 'Скейтборд',
                        'category' => 'Предметы',
                        'view_box' => '240 405 160 55',
                        'width' => 160,
                        'height' => 55,
                        'svg_content' => '<g>
  <path d="M250,420 Q320,430 390,420" stroke="#8B5CF6" stroke-width="14" stroke-linecap="round" fill="none"/>
  <circle cx="270" cy="445" r="12" fill="#F59E0B"/>
  <circle cx="370" cy="445" r="12" fill="#F59E0B"/>
</g>',
                        'tags' => ['скейт', 'спорт', 'улица'],
                    ],
                    [
                        'title' => 'Бумбокс',
                        'category' => 'Предметы',
                        'view_box' => '460 350 180 130',
                        'width' => 180,
                        'height' => 130,
                        'svg_content' => '<g>
  <rect x="470" y="380" width="160" height="90" rx="10" fill="#1E293B"/>
  <circle cx="510" cy="425" r="26" fill="#3B82F6" stroke="#93C5FD" stroke-width="4"/>
  <circle cx="590" cy="425" r="26" fill="#3B82F6" stroke="#93C5FD" stroke-width="4"/>
  <rect x="535" y="390" width="30" height="20" rx="3" fill="#64748B"/>
  <path d="M510,380 L510,360 L590,360 L590,380" stroke="#64748B" stroke-width="6" fill="none"/>
</g>',
                        'tags' => ['музыка', 'бумбокс', 'звук'],
                    ],
                ],
            ],

            // 14. Цветочная лавка
            [
                'title' => 'Цветочный киоск',
                'category' => 'Природа и Отдых',
                'description' => 'Деревянный прилавок с вазами цветов и букетами',
                'svg_content' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600" width="100%" height="100%">
  <rect width="800" height="600" fill="#FDF2F8"/>
  <rect x="0" y="470" width="800" height="130" fill="#FCE7F3"/>

  <!-- Прилавок с цветами -->
  <g id="flower_stand" data-name="Цветочный прилавок" data-category="Мебель">
    <rect x="220" y="340" width="360" height="130" fill="#B45309" rx="6"/>
    <rect x="210" y="330" width="380" height="20" fill="#78350F" rx="4"/>
    <!-- Вазы с цветами -->
    <rect x="250" y="270" width="30" height="60" fill="#38BDF8" rx="4"/>
    <circle cx="265" cy="245" r="20" fill="#F43F5E"/>
    <circle cx="265" cy="245" r="8" fill="#FDE047"/>

    <rect x="340" y="250" width="35" height="80" fill="#A855F7" rx="4"/>
    <circle cx="357" cy="225" r="24" fill="#FBBF24"/>
    <circle cx="357" cy="225" r="10" fill="#78350F"/>

    <rect x="440" y="270" width="30" height="60" fill="#FB923C" rx="4"/>
    <circle cx="455" cy="240" r="20" fill="#EC4899"/>
  </g>
</svg>',
                'components' => [
                    [
                        'title' => 'Цветочный прилавок',
                        'category' => 'Мебель',
                        'view_box' => '200 200 400 275',
                        'width' => 400,
                        'height' => 275,
                        'svg_content' => '<g>
  <rect x="220" y="340" width="360" height="130" fill="#B45309" rx="6"/>
  <rect x="210" y="330" width="380" height="20" fill="#78350F" rx="4"/>
  <rect x="250" y="270" width="30" height="60" fill="#38BDF8" rx="4"/>
  <circle cx="265" cy="245" r="20" fill="#F43F5E"/>
  <circle cx="265" cy="245" r="8" fill="#FDE047"/>
  <rect x="340" y="250" width="35" height="80" fill="#A855F7" rx="4"/>
  <circle cx="357" cy="225" r="24" fill="#FBBF24"/>
  <circle cx="357" cy="225" r="10" fill="#78350F"/>
  <rect x="440" y="270" width="30" height="60" fill="#FB923C" rx="4"/>
  <circle cx="455" cy="240" r="20" fill="#EC4899"/>
</g>',
                        'tags' => ['цветы', 'прилавок', 'магазин'],
                    ],
                ],
            ],

            // 15. Тележка мороженого
            [
                'title' => 'Тележка мороженого',
                'category' => 'Еда и Напитки',
                'description' => 'Винтажная тележка с рожками джелато',
                'svg_content' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600" width="100%" height="100%">
  <rect width="800" height="600" fill="#EFF6FF"/>
  <rect x="0" y="480" width="800" height="120" fill="#DBEAFE"/>

  <!-- Тележка мороженого -->
  <g id="ice_cream_cart" data-name="Тележка мороженого" data-category="Предметы">
    <rect x="260" y="320" width="280" height="150" rx="10" fill="#EC4899"/>
    <!-- Колесо -->
    <circle cx="330" cy="470" r="45" fill="#FFFFFF" stroke="#BE185D" stroke-width="8"/>
    <circle cx="330" cy="470" r="8" fill="#BE185D"/>
    <!-- Полосатый навес -->
    <path d="M230,220 L570,220 L540,260 L260,260 Z" fill="#F43F5E"/>
    <line x1="270" y1="260" x2="270" y2="320" stroke="#64748B" stroke-width="6"/>
    <line x1="530" y1="260" x2="530" y2="320" stroke="#64748B" stroke-width="6"/>
    <!-- Рожок мороженого на вывеске -->
    <polygon points="390,370 410,370 400,410" fill="#F59E0B"/>
    <circle cx="400" cy="360" r="16" fill="#FDE047"/>
  </g>
</svg>',
                'components' => [
                    [
                        'title' => 'Тележка мороженого',
                        'category' => 'Предметы',
                        'view_box' => '220 215 360 305',
                        'width' => 360,
                        'height' => 305,
                        'svg_content' => '<g>
  <rect x="260" y="320" width="280" height="150" rx="10" fill="#EC4899"/>
  <circle cx="330" cy="470" r="45" fill="#FFFFFF" stroke="#BE185D" stroke-width="8"/>
  <circle cx="330" cy="470" r="8" fill="#BE185D"/>
  <path d="M230,220 L570,220 L540,260 L260,260 Z" fill="#F43F5E"/>
  <line x1="270" y1="260" x2="270" y2="320" stroke="#64748B" stroke-width="6"/>
  <line x1="530" y1="260" x2="530" y2="320" stroke="#64748B" stroke-width="6"/>
  <polygon points="390,370 410,370 400,410" fill="#F59E0B"/>
  <circle cx="400" cy="360" r="16" fill="#FDE047"/>
</g>',
                        'tags' => ['мороженое', 'еда', 'лето'],
                    ],
                ],
            ],

            // 16. Пикник на поляне
            [
                'title' => 'Пикник на траве',
                'category' => 'Природа и Отдых',
                'description' => 'Клетчатый плед, корзинка для пикника и термос',
                'svg_content' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600" width="100%" height="100%">
  <rect width="800" height="600" fill="#ECFDF5"/>
  <rect x="0" y="450" width="800" height="150" fill="#059669"/>

  <!-- Плед в клетку -->
  <g id="picnic_blanket" data-name="Плед для пикника" data-category="Предметы">
    <polygon points="180,480 340,430 620,430 500,530" fill="#EF4444"/>
    <polygon points="260,455 340,430 480,430 400,480" fill="#FFFFFF" opacity="0.4"/>
  </g>

  <!-- Корзина для пикника -->
  <g id="picnic_basket" data-name="Корзина для пикника" data-category="Предметы">
    <polygon points="360,470 370,410 440,410 450,470" fill="#D97706" stroke="#92400E" stroke-width="3"/>
    <path d="M380,410 Q405,370 430,410" stroke="#92400E" stroke-width="5" fill="none"/>
  </g>
</svg>',
                'components' => [
                    [
                        'title' => 'Корзина для пикника',
                        'category' => 'Предметы',
                        'view_box' => '350 365 110 115',
                        'width' => 110,
                        'height' => 115,
                        'svg_content' => '<g>
  <polygon points="360,470 370,410 440,410 450,470" fill="#D97706" stroke="#92400E" stroke-width="3"/>
  <path d="M380,410 Q405,370 430,410" stroke="#92400E" stroke-width="5" fill="none"/>
</g>',
                        'tags' => ['пикник', 'корзина', 'отдых'],
                    ],
                ],
            ],

            // 17. Музыкальный лаундж
            [
                'title' => 'Виниловый лаундж',
                'category' => 'Искусство и Хобби',
                'description' => 'Проигрыватель винила, пластинка и акустическая гитара',
                'svg_content' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600" width="100%" height="100%">
  <rect width="800" height="600" fill="#F8FAFC"/>
  <rect x="0" y="480" width="800" height="120" fill="#E2E8F0"/>

  <!-- Акустическая гитара -->
  <g id="acoustic_guitar" data-name="Акустическая гитара" data-category="Предметы">
    <!-- Корпус гитары -->
    <ellipse cx="320" cy="380" rx="55" ry="65" fill="#D97706"/>
    <ellipse cx="320" cy="290" rx="42" ry="48" fill="#D97706"/>
    <circle cx="320" cy="310" r="18" fill="#1E293B"/>
    <!-- Гриф гитары -->
    <rect x="313" y="120" width="14" height="150" fill="#78350F"/>
    <rect x="306" y="90" width="28" height="36" rx="4" fill="#B45309"/>
  </g>

  <!-- Виниловый проигрыватель -->
  <g id="vinyl_turntable" data-name="Проигрыватель винила" data-category="Предметы">
    <rect x="440" y="370" width="220" height="110" rx="8" fill="#1E293B"/>
    <circle cx="530" cy="425" r="45" fill="#0F172A" stroke="#334155" stroke-width="6"/>
    <circle cx="530" cy="425" r="14" fill="#EF4444"/>
    <path d="M620,400 L560,425" stroke="#94A3B8" stroke-width="5" stroke-linecap="round"/>
  </g>
</svg>',
                'components' => [
                    [
                        'title' => 'Акустическая гитара',
                        'category' => 'Предметы',
                        'view_box' => '260 85 120 370',
                        'width' => 120,
                        'height' => 370,
                        'svg_content' => '<g>
  <ellipse cx="320" cy="380" rx="55" ry="65" fill="#D97706"/>
  <ellipse cx="320" cy="290" rx="42" ry="48" fill="#D97706"/>
  <circle cx="320" cy="310" r="18" fill="#1E293B"/>
  <rect x="313" y="120" width="14" height="150" fill="#78350F"/>
  <rect x="306" y="90" width="28" height="36" rx="4" fill="#B45309"/>
</g>',
                        'tags' => ['гитара', 'музыка', 'инструмент'],
                    ],
                ],
            ],

            // 18. Площадка для питомцев
            [
                'title' => 'Парк для питомцев',
                'category' => 'Животные',
                'description' => 'Жизнерадостный корги и игровой комплекс',
                'svg_content' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600" width="100%" height="100%">
  <rect width="800" height="600" fill="#FEF9C3"/>
  <rect x="0" y="470" width="800" height="130" fill="#10B981"/>

  <!-- Собака корги -->
  <g id="corgi_dog" data-name="Собака корги" data-category="Персонажи">
    <!-- Тело -->
    <rect x="320" y="380" width="130" height="70" rx="30" fill="#F59E0B"/>
    <!-- Голова -->
    <ellipse cx="440" cy="360" rx="32" ry="26" fill="#F59E0B"/>
    <!-- Мордочка -->
    <polygon points="460,355 480,365 460,375" fill="#FFFFFF"/>
    <circle cx="478" cy="365" r="5" fill="#0F172A"/>
    <!-- Уши -->
    <polygon points="420,345 428,310 442,340" fill="#F59E0B"/>
    <polygon points="445,340 458,310 465,345" fill="#F59E0B"/>
    <!-- Лапки -->
    <rect x="335" y="440" width="16" height="35" rx="6" fill="#FFFFFF"/>
    <rect x="365" y="440" width="16" height="35" rx="6" fill="#FFFFFF"/>
    <rect x="410" y="440" width="16" height="35" rx="6" fill="#FFFFFF"/>
    <!-- Хвостик -->
    <circle cx="315" cy="390" r="10" fill="#FFFFFF"/>
  </g>

  <!-- Миска для корма -->
  <g id="pet_bowl" data-name="Миска для корма" data-category="Предметы">
    <ellipse cx="530" cy="470" rx="35" ry="12" fill="#EF4444"/>
    <ellipse cx="530" cy="465" rx="28" ry="8" fill="#FEE2E2"/>
  </g>
</svg>',
                'components' => [
                    [
                        'title' => 'Собака корги',
                        'category' => 'Персонажи',
                        'view_box' => '305 305 185 175',
                        'width' => 185,
                        'height' => 175,
                        'svg_content' => '<g>
  <rect x="320" y="380" width="130" height="70" rx="30" fill="#F59E0B"/>
  <ellipse cx="440" cy="360" rx="32" ry="26" fill="#F59E0B"/>
  <polygon points="460,355 480,365 460,375" fill="#FFFFFF"/>
  <circle cx="478" cy="365" r="5" fill="#0F172A"/>
  <polygon points="420,345 428,310 442,340" fill="#F59E0B"/>
  <polygon points="445,340 458,310 465,345" fill="#F59E0B"/>
  <rect x="335" y="440" width="16" height="35" rx="6" fill="#FFFFFF"/>
  <rect x="365" y="440" width="16" height="35" rx="6" fill="#FFFFFF"/>
  <rect x="410" y="440" width="16" height="35" rx="6" fill="#FFFFFF"/>
  <circle cx="315" cy="390" r="10" fill="#FFFFFF"/>
</g>',
                        'tags' => ['собака', 'корги', 'животные'],
                    ],
                ],
            ],

            // 19. Лесной кемпинг
            [
                'title' => 'Лесной кемпинг',
                'category' => 'Природа и Отдых',
                'description' => 'Туристическая палатка, костер и сосна под луной',
                'svg_content' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600" width="100%" height="100%">
  <rect width="800" height="600" fill="#0F172A"/>
  <rect x="0" y="470" width="800" height="130" fill="#1E293B"/>

  <!-- Луна и звезды -->
  <circle cx="150" cy="140" r="40" fill="#FDE047"/>
  <circle cx="165" cy="135" r="35" fill="#0F172A"/>

  <!-- Палатка -->
  <g id="camping_tent" data-name="Туристическая палатка" data-category="Предметы">
    <polygon points="260,470 370,290 480,470" fill="#0284C7"/>
    <polygon points="370,290 480,470 410,470" fill="#0369A1"/>
    <polygon points="340,470 370,350 400,470" fill="#0F172A"/>
  </g>

  <!-- Костер -->
  <g id="campfire" data-name="Костер" data-category="Предметы">
    <line x1="530" y1="470" x2="590" y2="455" stroke="#78350F" stroke-width="8" stroke-linecap="round"/>
    <line x1="590" y1="470" x2="530" y2="455" stroke="#78350F" stroke-width="8" stroke-linecap="round"/>
    <polygon points="560,410 540,460 580,460" fill="#EF4444"/>
    <polygon points="560,425 548,460 572,460" fill="#F59E0B"/>
    <polygon points="560,440 554,460 566,460" fill="#FDE047"/>
  </g>
</svg>',
                'components' => [
                    [
                        'title' => 'Туристическая палатка',
                        'category' => 'Предметы',
                        'view_box' => '250 280 240 195',
                        'width' => 240,
                        'height' => 195,
                        'svg_content' => '<g>
  <polygon points="260,470 370,290 480,470" fill="#0284C7"/>
  <polygon points="370,290 480,470 410,470" fill="#0369A1"/>
  <polygon points="340,470 370,350 400,470" fill="#0F172A"/>
</g>',
                        'tags' => ['палатка', 'кемпинг', 'поход'],
                    ],
                    [
                        'title' => 'Костер',
                        'category' => 'Предметы',
                        'view_box' => '520 400 80 80',
                        'width' => 80,
                        'height' => 80,
                        'svg_content' => '<g>
  <line x1="530" y1="470" x2="590" y2="455" stroke="#78350F" stroke-width="8" stroke-linecap="round"/>
  <line x1="590" y1="470" x2="530" y2="455" stroke="#78350F" stroke-width="8" stroke-linecap="round"/>
  <polygon points="560,410 540,460 580,460" fill="#EF4444"/>
  <polygon points="560,425 548,460 572,460" fill="#F59E0B"/>
  <polygon points="560,440 554,460 566,460" fill="#FDE047"/>
</g>',
                        'tags' => ['огонь', 'костер', 'поход'],
                    ],
                ],
            ],

            // 20. Чемодан путешественника
            [
                'title' => 'Путешествие и багаж',
                'category' => 'Путешествия',
                'description' => 'Винтажный чемодан с наклейками, фотоаппарат и компас',
                'svg_content' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600" width="100%" height="100%">
  <rect width="800" height="600" fill="#FFF7ED"/>
  <rect x="0" y="480" width="800" height="120" fill="#FED7AA"/>

  <!-- Винтажный чемодан -->
  <g id="vintage_suitcase" data-name="Чемодан путешественника" data-category="Предметы">
    <rect x="250" y="320" width="300" height="160" rx="16" fill="#B45309"/>
    <!-- Ручка чемодана -->
    <path d="M370,320 L370,290 L430,290 L430,320" stroke="#78350F" stroke-width="10" fill="none"/>
    <!-- Ремни -->
    <rect x="310" y="320" width="20" height="160" fill="#78350F"/>
    <rect x="470" y="320" width="20" height="160" fill="#78350F"/>
    <!-- Наклейки -->
    <circle cx="360" cy="400" r="22" fill="#EF4444"/>
    <rect x="410" y="380" width="36" height="36" rx="4" fill="#3B82F6"/>
  </g>

  <!-- Фотоаппарат ретро -->
  <g id="vintage_camera" data-name="Ретро-фотоаппарат" data-category="Предметы">
    <rect x="580" y="410" width="100" height="70" rx="8" fill="#1E293B"/>
    <circle cx="630" cy="445" r="22" fill="#64748B" stroke="#94A3B8" stroke-width="4"/>
    <circle cx="630" cy="445" r="10" fill="#0F172A"/>
    <rect x="655" y="400" width="16" height="10" fill="#EF4444" rx="2"/>
  </g>
</svg>',
                'components' => [
                    [
                        'title' => 'Чемодан путешественника',
                        'category' => 'Предметы',
                        'view_box' => '240 280 320 205',
                        'width' => 320,
                        'height' => 205,
                        'svg_content' => '<g>
  <rect x="250" y="320" width="300" height="160" rx="16" fill="#B45309"/>
  <path d="M370,320 L370,290 L430,290 L430,320" stroke="#78350F" stroke-width="10" fill="none"/>
  <rect x="310" y="320" width="20" height="160" fill="#78350F"/>
  <rect x="470" y="320" width="20" height="160" fill="#78350F"/>
  <circle cx="360" cy="400" r="22" fill="#EF4444"/>
  <rect x="410" y="380" width="36" height="36" rx="4" fill="#3B82F6"/>
</g>',
                        'tags' => ['чемодан', 'путешествия', 'багаж'],
                    ],
                    [
                        'title' => 'Ретро-фотоаппарат',
                        'category' => 'Предметы',
                        'view_box' => '570 395 120 90',
                        'width' => 120,
                        'height' => 90,
                        'svg_content' => '<g>
  <rect x="580" y="410" width="100" height="70" rx="8" fill="#1E293B"/>
  <circle cx="630" cy="445" r="22" fill="#64748B" stroke="#94A3B8" stroke-width="4"/>
  <circle cx="630" cy="445" r="10" fill="#0F172A"/>
  <rect x="655" y="400" width="16" height="10" fill="#EF4444" rx="2"/>
</g>',
                        'tags' => ['камера', 'фото', 'ретро'],
                    ],
                ],
            ],
        ];
    }
}
