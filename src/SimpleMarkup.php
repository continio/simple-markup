<?php

namespace Continio\SimpleMarkup;

class SimpleMarkup
{
    /**
     * The original string passed to the parser.
     *
     * @var string
     */
    protected string $original;

    /**
     * The result of the applied markup filters.
     *
     * @var string
     */
    protected string $result;

    /**
     * The pattern to detect links.
     *
     * @var string
     */
    const LINK_PATTERN = '/\b((?:https?|ftp):\/\/[^\s<]+[^\s<.)])/i';

    /**
     * The tags that are allowed to be included in the html result.
     *
     * @var array|string[]
     */
    protected array $allowedTags = [
        '<strong>',
        '<u>',
        '<em>',
        '<del>',
        '<a>',
    ];

    /**
     * The HTML templates for each filter.
     *
     * @var array|string[]
     */
    protected array $templates = [
        'links' => '<a href="$1" target="_blank" rel="nofollow">$1</a>',
        'bold' => '<strong>$1</strong>',
        'underline' => '<u>$1</u>',
        'italic' => '<em>$1</em>',
        'del' => '<del>$1</del>',
    ];

    /**
     * The applied filters.
     *
     * @var array|string[]
     */
    protected array $applied = [];

    public function __construct(string $value)
    {
        $this->original = $value;
        $this->result = htmlentities($value);
    }

    /**
     * Reset the string to the original.
     *
     * @return $this
     */
    public function reset(): self
    {
        $this->applied = [];
        $this->result = $this->original;
        return $this;
    }

    /**
     * Customise a template.
     *
     * @param string $key
     * @param string $value
     * @return $this
     * @throws TemplateNotFoundException
     */
    public function setTemplate(string $key, string $value): self
    {
        if (! in_array($key, array_keys($this->templates))) {
            throw new TemplateNotFoundException("The template [$key] does not exist.");
        }

        $this->templates[$key] = $value;
        return $this;
    }

    /**
     * Add a single tag to the allowed tags array.
     *
     * @param string $tag
     * @return $this
     */
    public function addAllowedTag(string $tag): self
    {
        $this->allowedTags[] = $tag;
        return $this;
    }

    /**
     * Override all the allowed tags.
     *
     * @param array $tags
     * @return $this
     */
    public function setAllowedTags(array $tags): self
    {
        $this->allowedTags = $tags;
        return $this;
    }

    /**
     * Determine if changes have been made.
     *
     * @return bool
     */
    public function isDirty(): bool
    {
        return $this->result !== $this->original;
    }

    /**
     * Check if a specific filter has been applied.
     *
     * @param string $key
     * @return bool
     */
    public function filterIsApplied(string $key): bool
    {
        return in_array($key, $this->applied);
    }

    /**
     * Replace all links in the text.
     *
     * @return $this
     * @throws TemplateNotSetException
     */
    public function links(): self
    {
        if (! isset($this->templates['links'])) {
            throw new TemplateNotSetException("The [links] template is not set.");
        }

        $this->applied[] = 'links';
        $this->result = preg_replace(static::LINK_PATTERN, $this->templates['links'], $this->result);
        return $this;
    }

    /**
     * Apply the bold filter.
     *
     * @return $this
     * @throws TemplateNotSetException
     */
    public function bold(): self
    {
        if (! isset($this->templates['bold'])) {
            throw new TemplateNotSetException("The [bold] template is not set.");
        }

        $this->applied[] = 'bold';
        $this->result = preg_replace('/(?<!\*)\*([^\*]+)\*(?!\*)/', $this->templates['bold'], $this->result);
        return $this;
    }

    /**
     * Apply the italic filter.
     *
     * @return $this
     * @throws TemplateNotSetException
     */
    public function italic(): self
    {
        if (! isset($this->templates['italic'])) {
            throw new TemplateNotSetException("The [links] template is not set.");
        }

        $this->applied[] = 'italic';
        $this->result = preg_replace('/(?<!~)~([^~]+)~(?!~)/', $this->templates['italic'], $this->result);
        return $this;
    }

    /**
     * Apply the underline filter.
     *
     * @return $this
     * @throws TemplateNotSetException
     */
    public function underline(): self
    {
        if (! isset($this->templates['underline'])) {
            throw new TemplateNotSetException("The [links] template is not set.");
        }

        $this->applied[] = 'underline';
        $this->result = preg_replace('/(?<!_)_([^_]+)_(?!_)/', $this->templates['underline'], $this->result);
        return $this;
    }

    /**
     * Apply the del filter.
     *
     * @return $this
     * @throws TemplateNotSetException
     */
    public function del(): self
    {
        if (! isset($this->templates['del'])) {
            throw new TemplateNotSetException("The [del] template is not set.");
        }

        $this->applied[] = 'del';
        $this->result = preg_replace('/(?<!-)-([^-]+)-(?!-)/', $this->templates['del'], $this->result);
        return $this;
    }

    /**
     * Replace new lines with HTML line breaks.
     *
     * @return $this
     */
    public function lineBreaks(bool $xhtml = true): self
    {
        $this->applied[] = 'lineBreaks';
        $this->result = nl2br($this->result, $xhtml);
        return $this;
    }

    /**
     * Return the result.
     *
     * @return $this
     */
    public function parse(): string
    {
        return strip_tags($this->result, $this->allowedTags);
    }

    /**
     * Output the instance as a string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->parse();
    }

    /**
     * Apply all the filters to the given value.
     *
     * @return SimpleMarkup
     * @throws TemplateNotSetException
     */
    public function all(): SimpleMarkup
    {
        return (new SimpleMarkup($this->original))
            ->bold()
            ->underline()
            ->italic()
            ->lineBreaks()
            ->del()
            ->links();
    }
}
