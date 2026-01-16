<?php

declare(strict_types=1);

namespace Looksmaxxer\Models\Payloads;

/**
 * Полезная нагрузка для контакта.
 */
readonly class ContactPayload implements AttachmentPayload
{
    /**
     * @param string $name Имя контакта.
     * @param int|null $contactId ID контакта в MAX.
     * @param string|null $vcfInfo Информация в формате VCF.
     * @param string|null $vcfPhone Телефон в формате VCF.
     */
    public function __construct(
        public string $name,
        public ?int $contactId = null,
        public ?string $vcfInfo = null,
        public ?string $vcfPhone = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: (string)$data['name'],
            contactId: isset($data['contact_id']) ? (int)$data['contact_id'] : null,
            vcfInfo: $data['vcf_info'] ?? null,
            vcfPhone: $data['vcf_phone'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'contact_id' => $this->contactId,
            'vcf_info' => $this->vcfInfo,
            'vcf_phone' => $this->vcfPhone,
        ], fn($v) => $v !== null);
    }
}
