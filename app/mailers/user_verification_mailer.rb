class UserVerificationMailer < ActionMailer::Base
  def on_accepted(user_id)
    @user_email = User.find(user_id).email
    mail(
        from: "informatica@masmadrid.org",
        to: @user_email,
        subject: 'Más Madrid, Datos verificados'
    )
  end

  def on_rejected(user_id)
    @user_email = User.find(user_id).email

    mail(
        from: "informatica@masmadrid.org",
        to: @user_email,
        subject: 'Más Madrid, no hemos podido realizar la verificación'
    )
  end
end




