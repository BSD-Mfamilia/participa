class CollaborationsMailer < ActionMailer::Base
  include Resque::Mailer
  default from: 'informatica@masmadrid.org'

  def creditcard_error_email(user)
    @brand_config = Rails.application.secrets.microcredits["brands"][Rails.application.secrets.microcredits["default_brand"]]
    @user = user
    mail(
      from: 'informatica@masmadrid.org',
      to: user.email,
      subject: 'Problema en el pago con tarjeta de su colaboración'
    ) do |format|
      format.text
      end
  end

  def creditcard_expired_email(user)
    @brand_config = Rails.application.secrets.microcredits["brands"][Rails.application.secrets.microcredits["default_brand"]]
    @user = user
    mail(
        from: 'informatica@masmadrid.org',
        to: user.email,
        subject: 'Problema en el pago con tarjeta de su colaboración'
    ) do |format|
      format.text
      end
  end

  def receipt_returned(user)
    @brand_config = Rails.application.secrets.microcredits["brands"][Rails.application.secrets.microcredits["default_brand"]]
    @user = user
    mail(
        from: 'informatica@masmadrid.org',
        to: user.email,
        subject: 'Problema en la domiciliación del recibo de su colaboración'
      ) do |format|
      format.text
      end
  end

  def receipt_suspended(user)
    @brand_config = Rails.application.secrets.microcredits["brands"][Rails.application.secrets.microcredits["default_brand"]]
    @user = user
    mail(
        from: 'informatica@masmadrid.org',
        to: user.email,
        subject: 'Problema en la domicilación de sus recibos, colaboración suspendida temporalmente'
      ) do |format|
      format.text
      end
  end

  def successful_collaboration(email)
    mail(
      from: 'informatica@masmadrid.org',
      to: email,
      subject: 'Gracias por su colaboración'
    ) do |format|
    format.text
    end
  end
end
