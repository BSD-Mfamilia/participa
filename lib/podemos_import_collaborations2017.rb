class PodemosImportCollaborations2017

 fields={
      :name => "NOMBRE",
      :surname1 => "APELLIDO1",
      :surname2 => "APELLIDO2",
      :dni => "DNI",
      :born => "FECHA DE NACIMIENTO",
      :tlf => "TELEFONO MOVIL",
      :email => "EMAIL",
      :gender =>"GENERO",
      :address => "DOMICILIO",
      :town => "MUNICIPIO",
      :postal_code => "CODIGO POSTAL",
      :province => "PROVINCIA",
      :amount => "IMPORTE MENSUAL",
      :iban_code => "CODIGO IBAN",
      :swift_code => "BIC/SWIFT", # NO EXISTENTE NI NECESARIO EN LOS ÚLTIFOMS FICHEROS PUES SE PUEDE CALCULAR A PARTIR DEL IBAN
      :entity_code => "ENTIDAD",
      :office_code => "OFICINA",
      :cc_code => "CC",
      :acount_code => "CUENTA",
      :ine_town => "MUNICIPIO INE",
      :donation_type => "FINANCIACION TERRITORIAL",
      :payment_type => "METODO DE PAGO",
      :payment_frecuency => "FRECUENCIA DE PAGO",
      :created_At => "CREADO"
    }

  def self.log_to_file(filename, text)
    File.open(filename, 'a') { |f| f.write(text) }
  end

  def self.process_row(fields=self.fields ,row)
    params = { document_vatid: row[fields[:dni]].strip.upcase,
      full_name: row[fields[:surname1]] || row[fields[:surname2]] ? "#{row[fields[:name]]} #{row[fields[:surname1]]} #{row[fields[:surname2]]}" : row[fields[:name]],
      email: row[fields[:email]],
      ccc_1: row[fields[:entity_code],
      ccc_2: row[fields[:office_code],
      ccc_3: row[fields[:cc_code]],
      ccc_4: row[fields[:account_code]],
      iban_1: row[fields[:iban_code]],
      iban_2: row[fields[:swift_code]] || "",
      payment_type: row[fields[:fields[:payment_type]] || 2,
      amount: row[fields[:amount]].to_i * 100.0,
      frequency: row[fields[:payment_frecuency]] || 1,  # 1 3 12
      created_at: DateTime.parse(row["Creado"]) || DateTime.now,
      row: row
    }

    self.create_collaboration(params)
  end

  def self.create_collaboration(params)
    # si el usuario tiene el mismo correo en colabora y participa...
    if User.exists?(email: params[:email])
      user = User.find_by_email params[:email]
      # ... y si tambien tiene el mismo documento, lo damos de alta
      if user.document_vatid == params[:document_vatid]
        c = Collaboration.new
        c.user = user
        c.amount = params[:amount]
        c.frequency = params[:frequency]
        c.created_at = params[:created_at]
        c.payment_type = params [:payment_type]
        c.ccc_entity = params[:ccc_1]
        c.ccc_office = params[:ccc_2]
        c.ccc_dc = params[:ccc_3]
        c.ccc_account = params[:ccc_4]
        c.iban_account = params[:iban_1]
        c.iban_bic = c.calculate_bic

        if c.valid?
          c.save
          self.log_to_file "#{Rails.root}/log/collaboration/valid.txt", params[:row]
        else
          # en caso de que tenga un iban_account pero no un iban_bic ...
          if c.errors.messages[:iban_bic].first == "no puede estar en blanco"
              self.log_to_file "#{Rails.root}/log/collaboration/valid_not_bic.txt", "#{params[:row]}"
            end
          else
            self.log_to_file "#{Rails.root}/log/collaboration/not_valid.txt", "#{params[:row]};#{c.errors.messages.to_s}"
          end
        end
      else
        # Si el usuario Existe
          # Si tiene colaboración económica se anula esta y se envia a un fichero ANULADO POR TENER COLABORACION EN PARTICIPA.
          # Si no tiene colaboración económica vincula esta y se envía a un fichero INVITAR A QUE HAGA LA COLABORACION POR PARTICIPA.
        # si concuerda el correo pero no el documento, comprobamos si su nombre es el mismo en colabora y participa
        if user.full_name.downcase == params[:full_name].downcase
          # en ese caso lo guardamos con el documento de participa
          params[:document_vatid] = user.document_vatid
          self.create_collaboration(params)
        else
          self.log_to_file "#{Rails.root}/log/collaboration/not_document.txt", params[:row]
        end
      end
    else
      # en cambio, si no concuerda el email pero si hay alguno documento
      if User.exists?(document_vatid: params[:document_vatid])
        user = User.find_by_document_vatid params[:document_vatid]
        # comprobamos si su nombre es el mismo en colabora y participa
        if user.full_name.downcase == params[:full_name].downcase
          # en ese caso lo guardamos con el email de participa
          params[:email] = user.email
          self.create_collaboration(params)
        else
          self.log_to_file "#{Rails.root}/log/collaboration/not_email.txt", params[:row]
        end
      else
        # por ultimo, usuarios de los que no tenemos ni el email ni el documento en participa
        #self.create_non_user params
        self.log_to_file "#{Rails.root}/log/collaboration/not_participation.txt", params[:row]
      end
    end
  end

  def self.init csv_file
    CSV.foreach(csv_file, headers: true) do |row|
      begin
        process_row row
      rescue
        self.log_to_file "#{Rails.root}/log/collaboration/exception.txt", row
      end
    end
  end

end
