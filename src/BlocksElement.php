<?php

namespace Intimation\Catalyst;

class BlocksElement extends Element
{
    const REGISTER = 'register';
    const UNREGISTER = 'unregister';
    const AUTODISCOVER = 'autodiscover';

    public function init()
    {
        if (array_key_exists(self::REGISTER, $this->config)) {
            $this->add($this->config[self::REGISTER]);
        }

        if (array_key_exists(self::UNREGISTER, $this->config)) {
            $this->remove($this->config[self::UNREGISTER]);
        }

        if (true === $this->config[self::AUTODISCOVER]) {
            $this->autodiscover();
        }
    }

    protected function add(array $blocks)
    {
        array_map('register_block_type', $blocks);
    }

    protected function remove(array $blocks)
    {
        array_map('unregister_block_type', $blocks);
    }

    protected function autodiscover()
    {
        $registered_blocks = [];

        $directories = apply_filters('catalyst_blocks_autodiscover_directories', [
            get_theme_file_path('template-parts/blocks'),
        ]);

        foreach ($directories as $directory) {
            if ( ! file_exists($directory)) {
                continue;
            }

            $blocks_directory = new \DirectoryIterator($directory);

            foreach ($blocks_directory as $block_file) {
                if ($block_file->isDot() || $block_file->isDir()) {
                    continue;
                }

                $block_file_headers = get_file_data($block_file->getPathname(), [
                    'title'                 => 'Title',
                    'description'           => 'Description',
                    'category'              => 'Category',
                    'icon'                  => 'Icon',
                    'keywords'              => 'Keywords',
                    'mode'                  => 'Mode',
                    'align'                 => 'Align',
                    'post_types'            => 'PostTypes',
                    'supports_mode'         => 'SupportsMode',
                    'supports_multiple'     => 'SupportsMultiple',
                    'supports_inner_blocks' => 'SupportsInnerBlocks',
                ]);

                if (empty($block_file_headers['title'])) {
                    continue;
                }

                $slug = str_replace('.php', '', $block_file->getFilename());

                if (in_array($slug, $registered_blocks, true)) {
                    continue;
                }

                $registered_blocks[] = $slug;

                $block_settings = [
                    'name'            => $slug,
                    'title'           => $block_file_headers['title'],
                    'description'     => $block_file_headers['description'],
                    'category'        => $block_file_headers['category'],
                    'icon'            => $block_file_headers['icon'],
                    'keywords'        => explode(',', str_replace(' ', '', $block_file_headers['keywords'])),
                    'mode'            => $block_file_headers['mode'],
                    'align'           => $block_file_headers['align'],
                    'render_template' => $block_file->getPathname(),
                ];

                if ( ! empty($block_file_headers['post_types'])) {
                    $block_settings['post_types'] = explode(',',
                        str_replace(' ', '', $block_file_headers['post_types']));
                }

                if ( ! empty($block_file_headers['supports_mode'])) {
                    $block_settings['supports']['mode'] = $block_file_headers['supports_mode'] === 'true';
                }

                if ( ! empty($block_file_headers['supports_multiple'])) {
                    $block_settings['supports']['multiple'] = $block_file_headers['supports_multiple'] === 'true';
                }

                if ( ! empty($block_file_headers['supports_inner_blocks'])) {
                    $block_settings['supports']['jsx'] = $block_file_headers['supports_inner_blocks'] === 'true';
                }

                acf_register_block_type(apply_filters("intimation/blocks/{$slug}/settings", $block_settings));
            }
        }
    }
}
