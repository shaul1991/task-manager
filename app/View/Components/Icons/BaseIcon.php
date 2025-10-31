<?php

namespace App\View\Components\Icons;

use Illuminate\View\Component;

/**
 * 아이콘 컴포넌트의 베이스 클래스
 *
 * 모든 아이콘 컴포넌트는 이 클래스를 상속받아 공통 기능 사용
 */
abstract class BaseIcon extends Component
{
    public string $size;
    public ?string $color;
    public string $svgClass;

    /**
     * Create a new component instance.
     *
     * @param string $size 아이콘 크기 (sm, md, lg, xl)
     * @param string|null $color 아이콘 색상 (Tailwind color name)
     * @param string $class 추가 CSS 클래스
     */
    public function __construct(
        string $size = 'md',
        ?string $color = null,
        string $class = ''
    ) {
        $this->size = $size;
        $this->color = $color;
        $this->svgClass = $this->buildSvgClass($size, $color, $class);
    }

    /**
     * SVG 클래스 문자열 생성
     */
    protected function buildSvgClass(string $size, ?string $color, string $customClass): string
    {
        $classes = [];

        // 사용자 정의 클래스가 있으면 최우선 적용
        if (!empty($customClass)) {
            return $customClass;
        }

        // 크기 매핑
        $sizeMap = [
            'sm' => 'w-4 h-4',
            'md' => 'w-5 h-5',
            'lg' => 'w-6 h-6',
            'xl' => 'w-8 h-8',
        ];

        $classes[] = $sizeMap[$size] ?? $sizeMap['md'];

        // 색상 매핑
        if ($color) {
            $classes[] = "text-{$color}-600";
        } else {
            $classes[] = 'text-current';
        }

        return implode(' ', $classes);
    }

    /**
     * Get the view / contents that represent the component.
     */
    abstract public function render();
}
