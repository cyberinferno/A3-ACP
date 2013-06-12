USE [ASD]
GO

/****** Object:  Table [dbo].[credits_table]    Script Date: 06/07/2013 19:55:43 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

SET ANSI_PADDING ON
GO

CREATE TABLE [dbo].[credits_table](
	[account_name] [varchar](255) NOT NULL,
	[char_name] [varchar](255) NOT NULL,
	[credits] [bigint] NOT NULL
) ON [PRIMARY]

GO

SET ANSI_PADDING OFF
GO

