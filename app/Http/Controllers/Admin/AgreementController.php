<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agreement;
use Illuminate\Http\Request;

class AgreementController extends Controller
{
    public function index()
    {
        $agreement = Agreement::first() ?? new Agreement();

        return view('admin.agreements.index', [
            'agreement' => $agreement,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'privacy_info' => ['nullable', 'string'],
            'tos_info' => ['nullable', 'string'],
        ]);

        $data['privacy_info'] = $this->sanitizeLegalHtml($data['privacy_info'] ?? null);
        $data['tos_info'] = $this->sanitizeLegalHtml($data['tos_info'] ?? null);

        $agreement = Agreement::first();

        if ($agreement) {
            $agreement->update($data);
        } else {
            Agreement::create($data);
        }

        return redirect()
            ->route('admin.agreements.index')
            ->with('status', 'Privacy Policy and Terms of Service updated successfully.');
    }

    private function sanitizeLegalHtml(?string $html): ?string
    {
        if ($html === null) {
            return null;
        }

        $html = trim($html);

        if ($html === '') {
            return null;
        }

        libxml_use_internal_errors(true);

        $document = new \DOMDocument('1.0', 'UTF-8');
        $document->loadHTML('<?xml encoding="utf-8" ?><!DOCTYPE html><html><body>'.$html.'</body></html>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        libxml_clear_errors();

        $this->stripDisallowedNodes($document);
        $this->sanitizeAnchors($document);

        $body = $document->getElementsByTagName('body')->item(0);

        if (! $body) {
            $safeText = trim(strip_tags($html));

            if ($safeText === '') {
                return null;
            }

            return '<p>'.htmlspecialchars($safeText, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'</p>';
        }

        $cleanHtml = '';
        foreach ($body->childNodes as $child) {
            $cleanHtml .= $document->saveHTML($child);
        }

        $cleanHtml = trim($cleanHtml);

        if ($cleanHtml === '') {
            return null;
        }

        return $cleanHtml;
    }

    private function stripDisallowedNodes(\DOMDocument $document): void
    {
        $allowedTags = [
            'html', 'body',
            'p', 'div', 'br', 'strong', 'b', 'em', 'i', 'u',
            'ul', 'ol', 'li', 'h2', 'h3', 'blockquote', 'a',
        ];

        $xpath = new \DOMXPath($document);
        $nodes = iterator_to_array($xpath->query('//*') ?: []);

        foreach (array_reverse($nodes) as $node) {
            if (! $node instanceof \DOMElement) {
                continue;
            }

            $tagName = strtolower($node->tagName);

            if (! in_array($tagName, $allowedTags, true)) {
                $this->unwrapNode($node);
                continue;
            }

            $this->removeUnsafeAttributes($node);
        }
    }

    private function removeUnsafeAttributes(\DOMElement $node): void
    {
        $allowedAttributes = [
            'a' => ['href'],
        ];

        for ($index = $node->attributes->length - 1; $index >= 0; $index--) {
            $attribute = $node->attributes->item($index);

            if (! $attribute) {
                continue;
            }

            $name = strtolower($attribute->name);
            $tagName = strtolower($node->tagName);
            $tagAllowed = $allowedAttributes[$tagName] ?? [];

            if (str_starts_with($name, 'on') || ! in_array($name, $tagAllowed, true)) {
                $node->removeAttributeNode($attribute);
            }
        }
    }

    private function sanitizeAnchors(\DOMDocument $document): void
    {
        $anchors = $document->getElementsByTagName('a');

        foreach ($anchors as $anchor) {
            $href = trim($anchor->getAttribute('href'));

            if ($href === '') {
                $anchor->removeAttribute('href');
                continue;
            }

            if ($this->isAllowedHref($href)) {
                continue;
            }

            $anchor->removeAttribute('href');
        }
    }

    private function isAllowedHref(string $href): bool
    {
        if (str_starts_with($href, '#') || str_starts_with($href, '/')) {
            return true;
        }

        $scheme = parse_url($href, PHP_URL_SCHEME);

        if ($scheme === null) {
            return true;
        }

        return in_array(strtolower($scheme), ['http', 'https', 'mailto'], true);
    }

    private function unwrapNode(\DOMElement $node): void
    {
        $parent = $node->parentNode;

        if (! $parent) {
            return;
        }

        while ($node->firstChild) {
            $parent->insertBefore($node->firstChild, $node);
        }

        $parent->removeChild($node);
    }
}
