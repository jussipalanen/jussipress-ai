/** @jsxRuntime classic */
/** @jsx wp.element.createElement */
import { useBlockProps } from '@wordpress/block-editor'

export default function Save({ attributes }) {
  const {
    title,
    description,
    buttonText,
    buttonUrl,
    backgroundType,
    backgroundImage,
    overlayOpacity,
  } = attributes

  const sectionStyle =
    backgroundType === 'image' && backgroundImage?.url
      ? { backgroundImage: `url(${backgroundImage.url})` }
      : {}

  const blockProps = useBlockProps.save({ className: 'hero' })

  return (
    <section {...blockProps} style={sectionStyle}>
      {backgroundType === 'image' && backgroundImage?.url && (
        <div className="hero__overlay" style={{ opacity: overlayOpacity / 100 }} />
      )}

      <div className="hero__inner">
        <RichText.Content tagName="h1" className="hero__title" value={title} />

        <RichText.Content tagName="p" className="hero__description" value={description} />

        {buttonText && (
          <div className="hero__actions">
            <a className="hero__button" href={buttonUrl || '#'}>
              {buttonText}
            </a>
          </div>
        )}
      </div>
    </section>
  )
}
