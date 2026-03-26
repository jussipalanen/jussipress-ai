/** @jsxRuntime classic */
/** @jsx wp.element.createElement */
import { __ } from '@wordpress/i18n'
import {
  useBlockProps,
  RichText,
  InspectorControls,
  MediaUpload,
  MediaUploadCheck,
} from '@wordpress/block-editor'
import {
  PanelBody,
  RangeControl,
  RadioControl,
  Button,
  TextControl,
} from '@wordpress/components'

export default function Edit({ attributes, setAttributes }) {
  const {
    title,
    description,
    buttonText,
    buttonUrl,
    backgroundType,
    backgroundImage,
    overlayOpacity,
  } = attributes

  const blockProps = useBlockProps({ className: 'hero' })

  const sectionStyle =
    backgroundType === 'image' && backgroundImage?.url
      ? { backgroundImage: `url(${backgroundImage.url})` }
      : {}

  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Background', 'sage')}>
          <RadioControl
            label={__('Background type', 'sage')}
            selected={backgroundType}
            options={[
              { label: __('Color', 'sage'), value: 'color' },
              { label: __('Image', 'sage'), value: 'image' },
            ]}
            onChange={(value) => setAttributes({ backgroundType: value })}
          />

          {backgroundType === 'image' && (
            <>
              <MediaUploadCheck>
                <MediaUpload
                  onSelect={(media) =>
                    setAttributes({
                      backgroundImage: { id: media.id, url: media.url },
                    })
                  }
                  allowedTypes={['image']}
                  value={backgroundImage?.id}
                  render={({ open }) => (
                    <div style={{ marginBottom: '8px' }}>
                      {backgroundImage?.url && (
                        <img
                          src={backgroundImage.url}
                          alt=""
                          style={{
                            width: '100%',
                            height: '80px',
                            objectFit: 'cover',
                            borderRadius: '4px',
                            marginBottom: '8px',
                          }}
                        />
                      )}
                      <Button variant="secondary" onClick={open}>
                        {backgroundImage?.url
                          ? __('Replace image', 'sage')
                          : __('Select image', 'sage')}
                      </Button>
                      {backgroundImage?.url && (
                        <Button
                          variant="link"
                          isDestructive
                          onClick={() => setAttributes({ backgroundImage: {} })}
                          style={{ marginLeft: '8px' }}
                        >
                          {__('Remove', 'sage')}
                        </Button>
                      )}
                    </div>
                  )}
                />
              </MediaUploadCheck>

              <RangeControl
                label={__('Overlay opacity (%)', 'sage')}
                value={overlayOpacity}
                onChange={(value) => setAttributes({ overlayOpacity: value })}
                min={0}
                max={100}
              />
            </>
          )}
        </PanelBody>

        <PanelBody title={__('Button', 'sage')}>
          <TextControl
            label={__('Button text', 'sage')}
            value={buttonText}
            onChange={(value) => setAttributes({ buttonText: value })}
          />
          <TextControl
            label={__('Button URL', 'sage')}
            value={buttonUrl}
            onChange={(value) => setAttributes({ buttonUrl: value })}
            type="url"
          />
        </PanelBody>
      </InspectorControls>

      <section {...blockProps} style={sectionStyle}>
        {backgroundType === 'image' && backgroundImage?.url && (
          <div
            className="hero__overlay"
            style={{ opacity: overlayOpacity / 100 }}
          />
        )}

        <div className="hero__inner">
          <RichText
            tagName="h1"
            className="hero__title"
            value={title}
            onChange={(value) => setAttributes({ title: value })}
            placeholder={__('Hero title…', 'sage')}
          />

          <RichText
            tagName="p"
            className="hero__description"
            value={description}
            onChange={(value) => setAttributes({ description: value })}
            placeholder={__('Hero description…', 'sage')}
          />

          {buttonText && (
            <div className="hero__actions">
              <a className="hero__button" href={buttonUrl || '#'}>
                {buttonText}
              </a>
            </div>
          )}
        </div>
      </section>
    </>
  )
}
