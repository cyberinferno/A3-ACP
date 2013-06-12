USE [ASD]
GO

/****** Object:  Table [dbo].[buy_uniq_code]    Script Date: 06/07/2013 19:51:25 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

SET ANSI_PADDING ON
GO

CREATE TABLE [dbo].[buy_uniq_code](
	[transaction_id] [varchar](255) NOT NULL,
	[item_code] [varchar](255) NOT NULL,
	[unique_code] [varchar](255) NOT NULL
) ON [PRIMARY]

GO

SET ANSI_PADDING OFF
GO


