class AddSmsValidationToMicrocreditLoan < ActiveRecord::Migration
  def change
    add_column :microcredit_loans, :sms_confirmation_token, :string
    add_index :microcredit_loans, :sms_confirmation_token, unique: true
    add_column :microcredit_loans, :confirmation_sms_sent_at, :datetime
    add_column :microcredit_loans, :sms_confirmed_at, :datetime
  end
end
